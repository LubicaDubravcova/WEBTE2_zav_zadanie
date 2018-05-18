<?php
class routeColorPalette
{
	// farba trasy
	public static $routeColor = "#999999";
	// farby progressov
	public static $subrouteColors = [
		"#FF0000",
		"#FFFF00",
		"#00FF00",
		"#00FFFF",
		"#0000FF",
		"#FF00FF"
	];

	// prida arby ako javascript premenne
	static function addToJavascript() {
		echo "<script>";
		echo "var ROUTE_COLOR = \"".routeColorPalette::$routeColor."\";\n";
		echo "var SUBROUTE_COLORS = ".json_encode(routeColorPalette::$subrouteColors).";";
		echo "</script>";
	}

}