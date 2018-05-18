var sel0Prev = 0;
var sel1Prev = 0;
var sel2Prev = 0;
var sel3Prev = 0;
var sel4Prev = 0;
var sel5Prev = 0;
function disableSelectOption(id, selectNum){
    //console.log("javascript started selectnum: " + selectNum);
    var option;
    var select;
    var sel1;
    var sel2;
    var sel3;
    var sel4;
    var sel5;
    var selPrev;

    switch (selectNum){
        case 0: select=document.getElementById('selectUser_0'); selPrev=sel0Prev; sel1=document.getElementById('selectUser_1'); sel2=document.getElementById('selectUser_2'); sel3=document.getElementById('selectUser_3'); sel4=document.getElementById('selectUser_4'); sel5=document.getElementById('selectUser_5'); break;
        case 1: select=document.getElementById('selectUser_1'); selPrev=sel1Prev; sel1=document.getElementById('selectUser_0'); sel2=document.getElementById('selectUser_2'); sel3=document.getElementById('selectUser_3'); sel4=document.getElementById('selectUser_4'); sel5=document.getElementById('selectUser_5'); break;
        case 2: select=document.getElementById('selectUser_2'); selPrev=sel2Prev; sel1=document.getElementById('selectUser_1'); sel2=document.getElementById('selectUser_0'); sel3=document.getElementById('selectUser_3'); sel4=document.getElementById('selectUser_4'); sel5=document.getElementById('selectUser_5'); break;
        case 3: select=document.getElementById('selectUser_3'); selPrev=sel3Prev; sel1=document.getElementById('selectUser_1'); sel2=document.getElementById('selectUser_2'); sel3=document.getElementById('selectUser_0'); sel4=document.getElementById('selectUser_4'); sel5=document.getElementById('selectUser_5'); break;
        case 4: select=document.getElementById('selectUser_4'); selPrev=sel4Prev; sel1=document.getElementById('selectUser_1'); sel2=document.getElementById('selectUser_2'); sel3=document.getElementById('selectUser_3'); sel4=document.getElementById('selectUser_0'); sel5=document.getElementById('selectUser_5'); break;
        case 5: select=document.getElementById('selectUser_5'); selPrev=sel5Prev; sel1=document.getElementById('selectUser_1'); sel2=document.getElementById('selectUser_2'); sel3=document.getElementById('selectUser_3'); sel4=document.getElementById('selectUser_4'); sel5=document.getElementById('selectUser_0'); break;
    }
    option = select.options[select.selectedIndex].value;

    var i;
    for (i = 0; i < sel1.length; i++) {
        if(sel1.options[i].value == option){
            sel1.options[i].style.display = "none";
        }
        if(sel2.options[i].value == option){
            sel2.options[i].style.display = "none";
        }
        if(sel3.options[i].value == option){
            sel3.options[i].style.display = "none";
        }
        if(sel4.options[i].value == option){
            sel4.options[i].style.display = "none";
        }
        if(sel5.options[i].value == option){
            sel5.options[i].style.display = "none";
        }

        if(sel1.options[i].value == selPrev){
            sel1.options[i].style.display = "";
        }
        if(sel2.options[i].value == selPrev){
            sel2.options[i].style.display = "";
        }
        if(sel3.options[i].value == selPrev){
            sel3.options[i].style.display = "";
        }
        if(sel4.options[i].value == selPrev){
            sel4.options[i].style.display = "";
        }
        if(sel5.options[i].value == selPrev){
            sel5.options[i].style.display = "";
        }
    }

    switch (selectNum){
        case 0: sel0Prev = option; break;
        case 1: sel1Prev = option; break;
        case 2: sel2Prev = option; break;
        case 3: sel3Prev = option; break;
        case 4: sel4Prev = option; break;
        case 5: sel5Prev = option; break;
    }
}