<?php

const ACTIVE = 1;
const INACTIVE = 0;
const ORDER_STATUS_UNPAID = 'unpaid';
const ORDER_STATUS_PAID = 'paid';
const ORDER_STATUS_COMPLETED = 'completed';
const ORDER_STATUS_SHIPPED = 'shipped';
const ORDER_STATUS_CANCELLED = 'cancelled';

const ORDER_STATUS_LIST = [
    ORDER_STATUS_UNPAID,
    ORDER_STATUS_PAID,
    ORDER_STATUS_CANCELLED,
    ORDER_STATUS_SHIPPED,
    ORDER_STATUS_COMPLETED
];

const PRODUCT_COL_SELECT = [
    'id', 'title', 'slug', 'description', 'image', 'price', 'published', 'created_at', 'updated_at'
];
