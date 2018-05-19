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
		"#FF00FF",
		"#7F0000",
		"#7F7F00",
		"#007F00",
		"#007F7F",
		"#00007F",
		"#7F007F"
	];

	// prida farby ako javascript premenne
	static function addToJavascript() {
		echo "<script>";
		echo "var ROUTE_COLOR = \"".routeColorPalette::$routeColor."\";\n";
		echo "var SUBROUTE_COLORS = ".json_encode(routeColorPalette::$subrouteColors).";";
		echo "</script>";
	}

}