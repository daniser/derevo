<?php

declare(strict_types=1);

namespace TTBooking\Derevo;

use Brick\Math\BigInteger;
use Brick\Math\RoundingMode;
use Illuminate\Database\Eloquent\Collection as BaseCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use TTBooking\Derevo\Relations\HasAncestors;
use TTBooking\Derevo\Relations\HasDescendants;
use TTBooking\Derevo\Relations\HasSiblings;

/**
 * @method static Builder roots(string[] $scope = [])
 * @method static Builder leaves(string[] $scope = [])
 * @method static Builder trunks(string[] $scope = [])
 * @property static $parent
 * @property Collection|static[] $children
 * @property Collection|static[] $ancestors
 * @property Collection|static[] $ancestorsAndSelf
 * @property Collection|static[] $descendants
 * @property Collection|static[] $descendantsAndSelf
 * @property Collection|static[] $siblings
 * @property Collection|static[] $siblingsAndSelf
 */
abstract class Node extends Model
{
    use Concerns\ColumnScoped,
        Concerns\HasRelationshipsWithinTree,
        Concerns\MonitorsTreeOverflow;

    protected const LEFT_BOUND = 0;

    protected const RIGHT_BOUND = null;

    protected const MOVE_CHILD = 0;

    protected const MOVE_LEFT = -1;

    protected const MOVE_RIGHT = 1;

    protected string $parentColumn = 'parent_id';

    protected string $leftColumn = 'lft';

    protected string $rightColumn = 'rgt';

    protected string $depthColumn = 'depth';

    /** @var string|string[] */
    protected $scoped = [];

    protected $guarded = ['id', 'parent_id', 'lft', 'rgt', 'depth'];

    public function getParentColumnName(): string
    {
        return $this->parentColumn;
    }

    public function getQualifiedParentColumnName(): string
    {
        return $this->qualifyColumn($this->getParentColumnName());
    }

    public function getLeftColumnName(): string
    {
        return $this->leftColumn;
    }

    public function getQualifiedLeftColumnName(): string
    {
        return $this->qualifyColumn($this->getLeftColumnName());
    }

    public function getRightColumnName(): string
    {
        return $this->rightColumn;
    }

    public function getQualifiedRightColumnName(): string
    {
        return $this->qualifyColumn($this->getRightColumnName());
    }

    public function getDepthColumnName(): string
    {
        return $this->depthColumn;
    }

    public function getQualifiedDepthColumnName(): string
    {
        return $this->qualifyColumn($this->getDepthColumnName());
    }

    public function getParentKey()
    {
        return $this->getAttribute($this->getParentColumnName());
    }

    public function getLeft(): ?BigInteger
    {
        return is_null($left = $this->getAttribute($this->getLeftColumnName())) ? $left : BigInteger::of($left);
    }

    public function getRight(): ?BigInteger
    {
        return is_null($right = $this->getAttribute($this->getRightColumnName())) ? $right : BigInteger::of($right);
    }

    public function getDepth(): ?int
    {
        return $this->getAttribute($this->getDepthColumnName());
    }

    public static function rootLeftBoundary(): BigInteger
    {
        return BigInteger::of(static::LEFT_BOUND);
    }

    public static function rootRightBoundary(): BigInteger
    {
        static $rightBound;
        $rightBound = static::RIGHT_BOUND
            ?? (new static)->newBaseQueryBuilder()->selectRaw('~0 as result')->value('result');

        return BigInteger::of($rightBound);
    }

    public function getLeftBoundary(): ?BigInteger
    {
        if (! is_null($leftSibling = $this->getLeftSibling())) {
            return $leftSibling->getRight();
        }

        if (! $this->isRoot()) {
            return $this->parent->getLeft();
        }

        return static::rootLeftBoundary();
    }

    public function getRightBoundary(): ?BigInteger
    {
        if (! is_null($rightSibling = $this->getRightSibling())) {
            return $rightSibling->getLeft();
        }

        if (! $this->isRoot()) {
            return $this->parent->getRight();
        }

        return static::rootRightBoundary();
    }

    public function getInnerSpace(): ?BigInteger
    {
        if (! is_null($left = $this->getLeft()) && ! is_null($right = $this->getRight())) {
            return $right->minus($left);
        }

        return null;
    }

    public function getLeftSpace(): ?BigInteger
    {
        if (! is_null($leftBound = $this->getLeftBoundary()) && ! is_null($left = $this->getLeft())) {
            return $left->minus($leftBound);
        }

        return null;
    }

    public function getRightSpace(): ?BigInteger
    {
        if (! is_null($right = $this->getRight()) && ! is_null($rightBound = $this->getRightBoundary())) {
            return $rightBound->minus($right);
        }

        return null;
    }

    public static function scopeRoots(Builder $query, array $scope = []): Builder
    {
        return $query->scoped($scope)->whereNull((new static)->getParentColumnName());
    }

    public static function scopeLeaves(Builder $query, array $scope = []): Builder
    {
        return $query->scoped($scope)->whereDoesntHave('children');
    }

    public static function scopeTrunks(Builder $query, array $scope = []): Builder
    {
        return $query->scoped($scope)
            ->whereNotNull((new static)->getParentColumnName())
            ->whereHas('children');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class, $this->getParentColumnName());
    }

    public function children(): HasMany
    {
        return $this->hasMany(static::class, $this->getParentColumnName());
    }

    public function ancestors(): HasAncestors
    {
        return $this->hasAncestors();
    }

    public function ancestorsAndSelf(): HasAncestors
    {
        return $this->ancestors()->andSelf();
    }

    public function descendants(): HasDescendants
    {
        return $this->hasDescendants();
    }

    public function descendantsAndSelf(): HasDescendants
    {
        return $this->descendants()->andSelf();
    }

    public function siblings(): HasSiblings
    {
        return $this->hasSiblings();
    }

    public function siblingsAndSelf(): HasSiblings
    {
        return $this->siblings()->andSelf();
    }

    /**
     * @param  string|string[]  $columns
     * @return Collection
     */
    public function getAncestors($columns = ['*']): BaseCollection
    {
        return $this->ancestors()->get($columns);
    }

    /**
     * @param  string|string[]  $columns
     * @return Collection
     */
    public function getAncestorsAndSelf($columns = ['*']): BaseCollection
    {
        return $this->ancestorsAndSelf()->get($columns);
    }

    /**
     * @param  string|string[]  $columns
     * @return Collection
     */
    public function getDescendants($columns = ['*']): BaseCollection
    {
        return $this->descendants()->get($columns);
    }

    /**
     * @param  string|string[]  $columns
     * @return Collection
     */
    public function getDescendantsAndSelf($columns = ['*']): BaseCollection
    {
        return $this->descendantsAndSelf()->get($columns);
    }

    /**
     * @param  string|string[]  $columns
     * @return Collection
     */
    public function getSiblings($columns = ['*']): BaseCollection
    {
        return $this->siblings()->get($columns);
    }

    /**
     * @param  string|string[]  $columns
     * @return Collection
     */
    public function getSiblingsAndSelf($columns = ['*']): BaseCollection
    {
        return $this->siblingsAndSelf()->get($columns);
    }

    public function isRoot(): bool
    {
        return is_null($this->getParentKey());
    }

    public function isLeaf(): bool
    {
        return $this->children()->doesntExist();
    }

    public function isTrunk(): bool
    {
        return ! $this->isRoot() && ! $this->isLeaf();
    }

    /**
     * @param  static  $other
     * @return bool
     */
    public function isDescendantOf(Node $other): bool
    {
        return
            $this->getLeft() > $other->getLeft() &&
            $this->getLeft() < $other->getRight() &&
            $this->inSameScope($other);
    }

    /**
     * @param  static  $other
     * @return bool
     */
    public function isSelfOrDescendantOf(Node $other): bool
    {
        return
            $this->getLeft() >= $other->getLeft() &&
            $this->getLeft() < $other->getRight() &&
            $this->inSameScope($other);
    }

    /**
     * @param  static  $other
     * @return bool
     */
    public function isAncestorOf(Node $other): bool
    {
        return
            $this->getLeft() < $other->getLeft() &&
            $this->getRight() > $other->getLeft() &&
            $this->inSameScope($other);
    }

    /**
     * @param  static  $other
     * @return bool
     */
    public function isSelfOrAncestorOf(Node $other): bool
    {
        return
            $this->getLeft() <= $other->getLeft() &&
            $this->getRight() > $other->getLeft() &&
            $this->inSameScope($other);
    }

    /**
     * @param  string[]  $scope
     * @return static|null
     */
    public static function getFirstRoot(array $scope = []): ?self
    {
        return static::roots($scope)
            ->orderBy((new static)->getLeftColumnName())
            ->first();
    }

    /**
     * @param  string[]  $scope
     * @return static|null
     */
    public static function getLastRoot(array $scope = []): ?self
    {
        return static::roots($scope)
            ->orderByDesc((new static)->getLeftColumnName())
            ->first();
    }

    /**
     * @return static|null
     */
    public function getLeftSibling(): ?self
    {
        return $this->siblings()
            ->where($this->getLeftColumnName(), '<', $this->getLeft())
            ->orderByDesc($this->getLeftColumnName())
            ->first();
    }

    /**
     * @return static|null
     */
    public function getRightSibling(): ?self
    {
        return $this->siblings()
            ->where($this->getLeftColumnName(), '>', $this->getRight())
            ->orderBy($this->getLeftColumnName())
            ->first();
    }

    /**
     * @return static|null
     */
    public function getFirstChild(): ?self
    {
        return $this->children()
            ->orderBy($this->getLeftColumnName())
            ->first();
    }

    /**
     * @return static|null
     */
    public function getLastChild(): ?self
    {
        return $this->children()
            ->orderByDesc($this->getLeftColumnName())
            ->first();
    }

    public function newCollection(array $models = []): Collection
    {
        return new Collection($models);
    }

    /**
     * Begin querying the node.
     *
     * @return \Illuminate\Database\Eloquent\Builder|Builder
     */
    public static function query(): Builder
    {
        return parent::query();
    }

    /**
     * Get a new query builder for the node's table.
     *
     * @return \Illuminate\Database\Eloquent\Builder|Builder
     */
    public function newQuery(): Builder
    {
        return parent::newQuery();
    }

    /**
     * Create a new Eloquent query builder for the node.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return Builder|static
     */
    public function newEloquentBuilder($query): Builder
    {
        return new Builder($query);
    }

    protected static function booted()
    {
        static::saving(function (Node $node) {
            if (! $node->exists || $node->isDirty($node->getParentColumnName())) {
                $node->moveTo($node->unsetRelation('parent')->parent);
            }
        });
    }

    /**
     * @param  static|null  $target
     * @param  int  $position
     * @return $this
     */
    protected function moveTo(self $target = null, int $position = self::MOVE_CHILD): self
    {
        $newBoundaries = $this->allocateWithin(...$this->resolveBoundaries($target, $position));
        $this->exists && $this->performSubtreeMove(...$newBoundaries);

        // TODO: lock rows between left and right boundaries
        return $this
            ->setLeft($newBoundaries[0])
            ->setRight($newBoundaries[1])
            ->setDepth($newBoundaries[2]);
    }

    /**
     * @param  static|null  $target
     * @param  int  $position
     * @return array
     */
    protected function resolveBoundaries(self $target = null, int $position = self::MOVE_CHILD): array
    {
        // Move node to the root
        if (is_null($target)) {
            $target = static::getLastRoot($this->getQualifiedScopedValues());
            $position = self::MOVE_RIGHT;
        }

        // Make node the first and only root
        if (is_null($target)) {
            return [static::rootLeftBoundary(), static::rootRightBoundary(), 0];
        }

        $position = $position <=> self::MOVE_CHILD;

        // Convert "move to children" action to "move to the right of last child"
        if ($position === self::MOVE_CHILD && ! is_null($lastChild = $target->getLastChild())) {
            $target = $lastChild;
            $position = self::MOVE_RIGHT;
        }

        // Find boundaries for a new node based on target and relative position
        switch ($position) {
            case self::MOVE_LEFT:
                return [$target->getLeftBoundary(), $target->getLeft(), $target->getDepth()];
            case self::MOVE_CHILD:
                return [$target->getLeft(), $target->getRight(), $target->getDepth() + 1];
            default:
                return [$target->getRight(), $target->getRightBoundary(), $target->getDepth()];
        }
    }

    protected function allocateWithin(?BigInteger $left, ?BigInteger $right, int $depth): array
    {
        $chunk = $right->minus($left)->dividedBy(3, RoundingMode::HALF_EVEN);

        return [$left->plus($chunk), $right->minus($chunk), $depth];
    }

    protected function performSubtreeMove(?BigInteger $newLeft, ?BigInteger $newRight, int $newDepth): self
    {
        $connection = $this->getConnection();
        $grammar = $connection->getQueryGrammar();

        $leftColumn = $grammar->wrap($unwrappedLeftColumn = $this->getLeftColumnName());
        $rightColumn = $grammar->wrap($unwrappedRightColumn = $this->getRightColumnName());
        $depthColumn = $grammar->wrap($unwrappedDepthColumn = $this->getDepthColumnName());

        $left = $this->getLeft();
        $right = $this->getRight();
        $depth = $this->getDepth();

        $scale = $newRight->minus($newLeft)->dividedBy($right->minus($left), RoundingMode::HALF_EVEN);
        $depthShift = $newDepth - $depth;

        $rawStatements = [
            $unwrappedLeftColumn => "{$newLeft} + ({$leftColumn} - {$left}) * {$scale}",
            $unwrappedRightColumn => "{$newLeft} + ({$rightColumn} - {$left}) * {$scale}",
            $unwrappedDepthColumn => "{$depthColumn} + {$depthShift}",
        ];

        $this->newQuery()
            ->whereBetween($unwrappedLeftColumn, [(string) $left, (string) $right])
            ->whereBetween($unwrappedRightColumn, [(string) $left, (string) $right])
            ->whereNotNull($unwrappedDepthColumn)
            ->update(array_map([$connection, 'raw'], $rawStatements));

        return $this;
    }

    protected function setLeft(?BigInteger $left): self
    {
        return $this->setAttribute($this->getLeftColumnName(), is_null($left) ? $left : (string) $left);
    }

    protected function setRight(?BigInteger $right): self
    {
        return $this->setAttribute($this->getRightColumnName(), is_null($right) ? $right : (string) $right);
    }

    protected function setDepth(int $depth): self
    {
        return $this->setAttribute($this->getDepthColumnName(), $depth);
    }
}
