<?php
namespace Ant\Payment\Contracts;

interface BillableItem {
    public function getItemId();

    public function getItemType();

    public function getName();

    public function getUnitPrice();
}