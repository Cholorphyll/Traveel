<?php

namespace App\DataStructures;

class KDTreeNode {
    public $point;
    public $item;
    public $left;
    public $right;
    public $axis;

    public function __construct($point, $item, $axis) {
        $this->point = $point;
        $this->item = $item;
        $this->axis = $axis;
        $this->left = null;
        $this->right = null;
    }
}
