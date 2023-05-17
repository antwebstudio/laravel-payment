<?php
namespace Ant\Payment\Contracts;

interface BillableItem {
    public function getName();

    public function getUnitPrice();
}