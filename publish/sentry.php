<?php

declare(strict_types=1);
/**
 *
 *
 * @author    耐小心 <i@naixiaoixn.com>
 * @time      2020/9/7 2:14 上午
 *
 * @copyright 2020 耐小心
 */

return [
    "dsn"          => env("SENTRY_DSN"),
    "breadcrumbs"  => [
        "sql_bindings" => false,
    ],
    "integrations" => [],
];
