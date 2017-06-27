<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

// 当生成伪静态时此文件会被系统覆盖；如果发生页面指向错误，可以调整下面的规则顺序；越靠前的规则优先级越高。




$route['(.+)\/(\d+)\/(\d+)\/(\d+)\/(\d+).html'] = 'show/index/id/$4/page/$5'; // 【内容页】 对应规则：/{pdirname}/{y}/{m}/{id}/{page}.html
$route['(.+)\/(\d+)\/(\d+)\/(\d+).html']        = 'show/index/id/$4'; // 【内容页】 对应规则：/{pdirname}/{y}/{m}/{id}.html
$route['(.+)\/read\/(\d+).html']                = 'extend/index/id/$2'; // 【扩展页】 对应规则：/{pdirname}/read/{id}.html
$route['(.+)\/p(\d+).html']                     = 'category/index/dir/$1/page/$2'; // 【栏目页】 对应规则：/{pdirname}/p{page}.html
$route['(.+)']                                  = 'category/index/dir/$1'; // 【栏目页】 对应规则：/{pdirname}/
