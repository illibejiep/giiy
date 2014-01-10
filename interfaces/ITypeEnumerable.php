<?php

interface ITypeEnumerable {
    /** @return object */
    public function getType();
    /** @return array */
    public function getTypesFields();
}