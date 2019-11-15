<?php
header("Content-type: text/css; charset: UTF-8");
?>

/*
THEME NAME: WP Inventory Manager Theme
DESCRIPTION: Custom Theme specifically for the WP Inventory Manager
VERSION: 2.0
AUTHOR: WP Inventory Manager
AUTHOR URI: https://www.wpinventory.com/

Use this article to enable them to choose their font(s)
@help: https://css-tricks.com/css-variables-with-php/

*/

/* CSS RESET RULES */
* {
    margin: 0;
    padding: 0;
    vertical-align: top;
}

body,
html {
    background: #fff;
}

body {
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: 18px;
}

h1,
h2,
h3,
h4,
h5,
h6 {
    font-weight: 900;
    display: block;
    margin: 0 0 30px;
    line-height: 1.3em;
}

h1 {
    font-size: 30px;
}

h2 {
    color: #2d89ab;
    font-size: 25px;
}

h3 {
    font-size: 22px;
}

body.search-results h3 {
    margin: 0;
}

body.search-results .hentry small.date {
    margin: 0 0 25px;
    display: block;
}

h4 {
    font-size: 18px;
}

p {
    margin: 0 0 30px;
    padding: 0;
}

a {
    text-decoration: underline;
    transition: all .5s ease;
    color: #2d89ab;
}

a:hover {
    text-decoration: none;
    color: #e2383f;
}

ul,
ol {
    margin: 0 0 30px;
    padding: 0;
}

li,
section.main-content aside li.widget li {
    list-style: none;
}

section.main-content aside li.widget {
    list-style: none;
    margin: 0 0 25px;
    border: 1px solid #fff;
}

section.main-content aside li.widget_wpinventory_latest_items_widget,
.wpinventory_advanced_search form {
    padding: 0;
}

section.main-content aside li.widget li {
    margin: 0;
}

section.main-content aside li.widget_wpinventory_latest_items_widget li.wpinventory_item {
    padding: 15px;
}

section.main-content aside li.widget_wpinventory_latest_items_widget li.wpinventory_item:nth-child(even) {
    background: #f1f1f1;
}

blockquote {
    max-width: 80%;
    margin: 0 auto 25px;
    background: #f1f1f1;
    padding: 50px;
    border-left: 5px solid #ccc;
    display: block;
}

.contentwrapper li {
    list-style: inherit;
    margin-left: 30px;
}

section.main-content aside li.custom_text li {
    list-style: disc;
}

hr {
    width: 100%;
    background: #2d89ab;
    height: 2px;
    font-size: 0;
    display: block;
    border: none;
    padding: 0;
    margin: 0 0 30px;

}

b,
strong {
    font-weight: 700;
}

.fa,
.fas {
    line-height: inherit;
}

img {
    max-width: 100%;
    height: auto;
}

header input#toggle,
header label.toggle {
    display: none;
}

.site_center {
    max-width: 1200px;
    padding: 0 2%;
    margin: 0 auto;
}

button,
.button,
input[type="submit"] {
    transition: background 0.5s ease;
    background: #2d89ab;
    color: #fff;
    text-transform: uppercase;
    padding: 12px;
    font-family: 'Montserrat', sans-serif;
    border: none;
    cursor: pointer;
    text-decoration: none;
}

button i,
.button i {
    transition: margin-left 0.5s ease;
    color: #2d89ab;
    display: inline-block;
    line-height: inherit;
}

button:hover,
.button:hover {
    background: #4889c8;
    color: #fff;
}

button:hover i,
.button:hover i {
    margin-left: 5px;
}

.alignright {
    float: right;
    margin: 0 0 15px 15px;
}

.alignleft {
    float: left;
    margin: 0 15px 15px 0;
}

.aligncenter {
    display: block;
    float: none;
    clear: both;
    margin: 0 auto 25px;
}

.alignnone {
    display: block;
    margin: 0 auto;
}

/*  Tables  */

table {
    border-spacing: 0;
}

thead {
    background: #f1f1f1;
}

tbody tr:nth-child(even) {
    background: #fdf9eb;
}

/*  Forms  */

input[type="text"],
input[type="email"],
input[type="phone"],
input[type="password"],
input[type="number"] {
    border: 1px solid #ccc;
    max-width: calc(100% - 26px); /* two pixels for the border left and right and 24 pixels for padding left and right */
    padding: 8px 12px;
}

textarea {
    border: 1px solid #ccc;
    padding: 12px;
}

/* Checkboxes and Radio buttons */
@keyframes click-wave {
    0% {
        height: 40px;
        width: 40px;
        opacity: 0.35;
        position: relative;
    }
    100% {
        height: 200px;
        width: 200px;
        margin-left: -80px;
        margin-top: -80px;
        opacity: 0;
    }
}

input[type="radio"],
input[type="checkbox"] {
    -webkit-appearance: none;
    -moz-appearance: none;
    -ms-appearance: none;
    -o-appearance: none;
    appearance: none;
    position: relative;
    height: 25px;
    width: 25px;
    transition: all 0.15s ease-out 0s;
    background: #cbd1d8;
    border: none;
    color: #fff;
    cursor: pointer;
    display: inline-block;
    margin-right: 0.5rem;
    outline: none;
    z-index: 1000;
}

input[type="radio"]:checked,
input[type="checkbox"]:checked,
input[type="radio"]:hover,
input[type="checkbox"]:hover {
    background: #40e0d0;
}

input[type="radio"]:checked::before,
input[type="checkbox"]:checked::before {
    position: absolute;
    content: '✔';
    display: inline-block;
    font-size: 16px;
    text-align: center;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

input[type="radio"]:checked::after,
input[type="checkbox"]:checked::after {
    -webkit-animation: click-wave 0.65s;
    -moz-animation: click-wave 0.65s;
    animation: click-wave 0.65s;
    background: #40e0d0;
    content: '';
    display: block;
    position: relative;
    z-index: 100;
}

input[type="radio"] {
    border-radius: 50%;
}

input[type="radio"]::after {
    border-radius: 50%;
}

.gform_wrapper ul.gfield_checkbox li input[type="checkbox"],
.gform_wrapper ul.gfield_radio li input[type="radio"] {
    width: 25px !important;
}

/* The container must be positioned relative: */
.dropdown-select {
    position: relative;
}

.dropdown-select select {
    display: none; /*hide original SELECT element: */
}

.select-selected {
    background: #2d89ab;
}

/* Style the arrow inside the select element: */
.select-selected:after {
    position: absolute;
    content: "";
    top: 14px;
    right: 10px;
    width: 0;
    height: 0;
    border: 6px solid transparent;
    border-color: #fff transparent transparent transparent;
}

/* Point the arrow upwards when the select box is open (active): */
.select-selected.select-arrow-active:after {
    border-color: transparent transparent #fff transparent;
    top: 7px;
}

/* style the items (options), including the selected item: */
.select-items div,
.select-selected {
    color: #ffffff;
    padding: 8px 16px;
    border: 1px solid transparent;
    border-color: transparent transparent rgba(0, 0, 0, 0.1) transparent;
    cursor: pointer;
}

/* Style items (options): */
.select-items {
    position: absolute;
    background: #2d89ab;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 99;
}

/* Hide the items when the select box is closed: */
.select-hide {
    display: none;
}

.select-items div:hover, .same-as-selected {
    background-color: rgba(0, 0, 0, 0.1);
}

/*  Header  */

.headerwrapper {
    position: relative;
    z-index: 99999999;
    box-shadow: 2px 2px 5px #ccc;
}

header #logo {
    max-width: 300px;
}

header .navwrapper {
    clear: both;
    font-size: 14px;
    font-weight: 700;
}

.headerwrapper.scrolled header {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
}

header nav li {
    display: inline-block;
    position: relative;
}

header nav li a {
    display: block;
    padding: 20px;
    text-transform: uppercase;
}

header nav li li a {
    /*border-bottom: 1px solid #aa893a !important;*/
    padding: 10px 20px;
}

header nav ul.sub-menu {
    /*border-top: 1px solid #aa893a !important;*/
}

header nav li.current_page_item a,
header nav li a:hover {
    /*background: #e8d896;*/
}

header nav ul {
    margin: 0;
}

header nav li ul,
header nav li ul ul {
    position: absolute;
    left: -9999em;
    width: 275px;
    text-align: left;
}

header nav li:hover ul {
    left: 0;
}

header nav ul ul li,
header nav ul ul li a {
    border: none;
}

header nav ul ul li {
    display: block;
}

header nav ul ul li a {
    text-transform: none;
}

.wpim_theme_social {
    float: right;
    text-align: right;
    font-size: 30px;
    margin: 20px 0 0;
}

.wpim_theme_social ul {
    margin: 0;
}

.wpim_theme_social li {
    display: inline-block;
    margin: 0 0 0 10px;
}

header li#social_label {
    display: block;
    font-size: 18px;
    font-weight: 300;
}

header .wpim_theme_contact {
    float: right;
    border-left: 1px solid #ccc;
    padding: 0 0 0 15px;
    margin: 20px 0 0 15px;
    font-size: 12px;
    line-height: 26px;
}

header .wpim_theme_contact span {
    display: block;
}

/* end header */

/*  Contentwrapper  */

.contentwrapper {
    padding: 70px 0;
    position: relative;
    overflow: hidden;
    overflow-y: scroll;
}

section.main-content figure img {
    -webkit-transition: -webkit-transform 0.4s;
    transition: transform 0.4s;
}

section.main-content figure img:hover {
    -webkit-transform: scale(1.2) rotate(0.01deg);
    transform: scale(1.2) rotate(0.01deg);
    max-width: none;
}

/*  Footer  */


/*  Main Content  */

section.main-content {
    max-width: 1200px;
    overflow: hidden;
    padding: 0 4%;
    margin: 0 auto;
    overflow-x: auto;
    overflow-y: scroll;
}

section.main-content span.sidebar_flyout {
    display: none;
    position: absolute;
    top: 0;
    right: 0;
    width: 50px;
    height: 50px;
    line-height: 50px;
    background: #2d89ab;
    color: #fff;
    cursor: pointer;
    text-align: center;
    padding: 0;
    margin: 0;
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
    -webkit-transition: .5s ease-in-out;
    -moz-transition: .5s ease-in-out;
    -o-transition: .5s ease-in-out;
    transition: .5s ease-in-out;
    z-index: 3;
}

.sbar_left span.sidebar_flyout {
    right: unset;
    left: 0;
}

.sbar_left span.sidebar_flyout.open {
    left: 230px;
}

section.main-content span.sidebar_flyout span {
    display: block;
    position: absolute;
    height: 5px;
    width: 100%;
    background: #fff;
    border-radius: 9px;
    opacity: 1;
    left: 11px;
    width: 30px;
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
    -webkit-transition: .25s ease-in-out;
    -moz-transition: .25s ease-in-out;
    -o-transition: .25s ease-in-out;
    transition: .25s ease-in-out;
}

section.main-content span.sidebar_flyout span:nth-child(1) {
    top: 12px;
}

section.main-content span.sidebar_flyout span:nth-child(2) {
    top: 22px;
}

section.main-content span.sidebar_flyout span:nth-child(3) {
    top: 32px;
}

section.main-content span.sidebar_flyout.open span:nth-child(1) {
    top: 24px;
    -webkit-transform: rotate(135deg);
    -moz-transform: rotate(135deg);
    -o-transform: rotate(135deg);
    transform: rotate(135deg);
}

section.main-content span.sidebar_flyout.open span:nth-child(2) {
    opacity: 0;
    left: -60px;
}

section.main-content span.sidebar_flyout.open span:nth-child(3) {
    top: 24px;
    -webkit-transform: rotate(-135deg);
    -moz-transform: rotate(-135deg);
    -o-transform: rotate(-135deg);
    transform: rotate(-135deg);
}

.sbar_left article,
.sbar_right article {
    max-width: 70%;
    float: left;
    overflow: hidden;
    overflow-x: auto;
}

.sbar_left article {
    float: right;
}

.sbar_left aside,
.sbar_right aside {
    max-width: 25%;
    float: right;
}

.sbar_left aside {
    float: left;
}

/*  end main content  */

/*  Testimonials  Widget  */

#sidebar-testimonials_sidebar {
    background: #d0bb7a;
    font-size: 25px;
    padding: 80px 0;
    text-align: center;
}

#sidebar-testimonials_sidebar ul {
    margin: 0 auto;
    max-width: 1200px;
}

/*  Blog  */

.archive .postwrapper {
    padding: 0;
    margin: 0 0 25px;
    border-bottom: 1px solid #4889c8;
}

.archive .postwrapper h2.entry-title {
    margin: 0;
}

/*  WPIM Filter  */

.wpinventory_filter {
    margin: 0 0 20px;
    padding: 15px;
    background: #f1f1f1;
    border: 1px solid #ccc;
    width: calc(100% - 32px);
}

section.main-content div.wpim-asf-field p {
    margin-bottom: 10px;
}

section.main-content div.wpim-asf-values {
    margin-left: 0;
}

.wpinventory_filter span.search {
    display: block;
    width: 100%;
    margin: 0 0 20px;
}

.wpinventory_filter span.search input {
    margin: 0 0 0 20px;
    width: 200px;
    border: 1px solid #ccc;
}

.wpinventory_filter span.sort label {
    padding: 8px 16px 5px 0;
    display: inline-block;
}

.wpinventory_filter span + span {
    margin: 0 10px 5px 0;
}

div.select-selected {
    display: inline-block;
    padding: 8px 30px 8px 15px;
    position: relative;
}

.wpinventory_loop_all_table {
    width: 100%;
}

.wpinventory_filter input.button {
    float: right;
}

.wpim-asf-clear-search {
    font-size: 14px;
    line-height: 40px;
    color: #888;
    font-weight: 300;
    text-decoration: none;
}

.wpim-asf-values label {
    position: relative;
    padding-left: 30px;
    display: block;
}

.wpim-asf-values label input {
    margin-left: -30px;
}

.wpim-asf-values div.select-selected {
    display: block;
}

/*  Latest Items  */

.widget_wpinventory_latest_items_widget p.inventory_number {
    display: none;
}

/*  WPIM pagination */
.wpinventory_pagination {
    text-align: center;
}

.wpinventory_pagination span.wpinventory_showing {
    margin: 0 10px 10px 0;
    line-height: 35px;
}

.wpinventory_pagination a {
    display: inline-block;
    width: 30px;
    height: 30px;
    line-height: 30px;
    padding: 0;
    margin: 0 10px 10px 0;
    text-align: center;
    background: #2d89ab;
    color: #fff;
    border-radius: 2px;
    font-size: 13px;
}

.wpinventory_pagination a.page_current,
.wpinventory_pagination a:hover {
    background: #e2383f;
}

.wpinventory_loop_all_div .wpinventory_item {
    min-width: unset;
}

.wpinventory_loop_all_div .wpinventory_item .wpim-add-to-cart-container {
    padding: 0 15px;
}

.wpinventory_loop_all_div .wpinventory_item input.submit.wpim-add-to-cart {
    width: 100%;
}

@media only screen and (max-width: 800px) {

    section.main-content span.sidebar_flyout {
        display: block;
    }

    section.main-content span.sidebar_flyout.open + article + aside {
        right: 0;
    }

    .sbar_left span.sidebar_flyout.open + article + aside {
        right: unset;
        left: 0;
    }

    .sbar_left article,
    .sbar_right article {
        float: none;
        max-width: none;
        display: block;
        width: 100%;
    }

    .sbar_left aside,
    .sbar_right aside {
        position: absolute;
        top: 0;
        right: -500px;
        width: 280px;
        background: #fff;
        box-shadow: -2px -2px 5px #ccc;
        max-width: none;
        z-index: 2;
    }

    .sbar_left aside {
        right: unset;
        left: -500px;
    }

    section.main-content aside li.widget {
        padding: 20px;
    }

    .navwrapper label.toggle {
        display: block;
        text-align: right;
        padding: 10px 20px;
        cursor: pointer;
        border-top: 1px solid #ccc;
    }

    body.fixnav label.toggle {
        position: fixed;
        top: 0;
        width: 96%;
        padding-left: 2%;
        padding-right: 2%;
    }

    header .navwrapper {
        width: 104%;
        margin: 0 0 0 -2%;
    }

    .navwrapper ul.menu {
        display: none;
    }

    .navwrapper input#toggle:checked + ul.menu {
        display: block;
    }

    header nav {
        text-align: left;
    }

    header nav li {
        display: block;
        border: none;
    }

    header nav li#nav_search {
        display: none;
    }

    header nav li a {
        border-bottom: 1px solid #aa893a;
    }

    header nav ul.sub-menu {
        border-top: none !important;
    }

    header nav ul ul {
        position: relative;
        left: auto;
        width: auto;
        display: block;
        background: #d0bb7a;
    }

    header nav ul ul a {
        padding-left: 30px;
    }

    ul.sub-menu a:hover {
        background: #e8d896;
    }
}

@media only screen and (max-width: 768px) {
    #logo {
        display: block;
        margin: 0 auto 20px;
    }

    header .wpim_theme_contact {
        margin: 0 0 20px;
        padding: 0;
        float: left;
        text-align: left;
        border: none;
    }

    header .wpim_theme_social {
        margin: 0 0 20px;
    }
}

@media only screen and (max-width: 600px) {
    .wpinventory_filter input.button {
        float: none;
        display: block;
        margin: 20px 0 0;
    }
}

@media only screen and (max-width: 450px) {
    .wpinventory_filter span.sort {
        display: block;
        width: 100%;
    }

    .wpinventory_filter span.search input {
        width: 150px;
    }

    header .wpim_theme_contact,
    header .wpim_theme_social {
        float: none;
        clear: both;
        margin: 0 0 20px;
        padding: 0;
        text-align: center;
    }
}