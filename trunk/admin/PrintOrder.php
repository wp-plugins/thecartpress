<?php
/**
 * This file is part of TheCartPress.
 * 
 * TheCartPress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TheCartPress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TheCartPress.  If not, see <http://www.gnu.org/licenses/>.
 */
session_start();
?>
<html>
<head>
<title><?php echo _('Order');?></title>
<style>
table {width: 90%;}

</style>
</head>
<body>
<?php echo $_SESSION['order_page']; ?>
<p>
<a href="javascript:print();"><?php echo _('print');?></a>
&nbsp;|&nbsp;
<a href="javascript:close();"><?php echo _('close');?></a>
</p>
</body>
</html>
