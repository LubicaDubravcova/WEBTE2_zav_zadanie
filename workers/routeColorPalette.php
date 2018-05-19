<?php
class routeColorPalette
{
	// farba trasy
	public static $routeColor = "#999999";
	// farby progressov
	public static $subrouteColors = [
		"#FF0000",
		"#FF7F00",
		"#FFFF00",
		"#7FFF00",
		"#00FF00",
		"#00FF7F",
		"#00FFFF",
		"#007FFF",
		"#0000FF",
		"#7F00FF"
	];

	// prida farby ako javascript premenne
	static function addToJavascript() {
		echo "<script>";
		echo "var ROUTE_COLOR = \"".routeColorPalette::$routeColor."\";\n";
		echo "var SUBROUTE_COLORS = ".json_encode(routeColorPalette::$subrouteColors).";";
		echo "</script>";
	}

}