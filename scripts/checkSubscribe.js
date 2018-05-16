$value1 = 1;
$value2 = 0;
function changeSubscribe1($id){
	$.ajax({
        url: "../subscribe.php",
        data: {id : $id, sub : $value1},
        type: "POST",
        success: function(data){
            }
    });
}

function changeSubscribe2($id){
	$.ajax({
        url: "../subscribe.php",
        data: {id : $id, sub : $value2},
        type: "POST",
        success: function(data){
            }
    });
}