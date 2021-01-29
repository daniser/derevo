<?php

return [

    'allocator' => [

        /**
         * Target maximum number of child nodes (default).
         * Use Node::allocateFor() method to allocate space for a given number of children.
         */
        'target_children' => env('DEREVO_TARGET_CHILDREN', 10),

        /**
         * Proportions in which available space will be allocated for use by descendent nodes.
         *
         * |----------------------------------------------------------------------------------------------------|
         * |                                       P A R E N T   N O D E                                        |
         * |----------------------------------------------------------------------------------------------------|
         * | left |   child 1 body   | spacing |   child 2 body   | spacing |   child 3 body   |     right      |
         * |----------------------------------------------------------------------------------------------------|
         */
        'ratios' => [
            'left'    => 0.5,
            'body'    => 1.0,
            'interim' => 0.5,
            'right'   => 2.0,
        ],

    ],

];
