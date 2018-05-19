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
		"#AF0000",
		"#AFAF00",
		"#00AF00",
		"#00AFAF",
		"#0000AF",
		"#AF00AF"
	];

	// prida farby ako javascript premenne
	static function addToJavascript() {
		echo "<script>";
		echo "var ROUTE_COLOR = \"".routeColorPalette::$routeColor."\";\n";
		echo "var SUBROUTE_COLORS = ".json_encode(routeColorPalette::$subrouteColors).";";
		echo "</script>";
	}

}