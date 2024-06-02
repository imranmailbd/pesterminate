function mathCaptcha(){
	var mathCaptchaHTML = '<style>.bGreen{border:2px solid green;outline: none;}.bRed{border:2px solid red;outline: none;}</style>'+
'<div style="width:300px;float:left;margin:0 15px 15px 0;font-size:24px;font-weight:bold;text-align:center;border:1px solid #CCC;background: linear-gradient(to right top, #EEE, #DDD, #F6F6F6);padding:15px;font-family: \'Brush Script MT\', \'Brush Script Std\', cursive;color:#000;">'+
'<div id="fNumber" style="width:30px;float:left;margin:0 5px;">0</div>'+
'<div style="width:10px;float:left;margin:0 5px;">+</div>'+
'<div id="lNumber" style="width:30px;float:left;margin:0 5px;">0</div>'+
'<div style="width:20px;float:left;margin:0 15px 0 5px;">=</div>'+
'<input required type="text" name="mathCaptcha" id="resultN" value="" style="width:80px;float:left;margin:0 5px;padding:5px;line-height:20px;">'+
'<div onClick="mathCaptcha();" style="width:20px;float:left;margin:0 10px 0 0px;cursor:pointer">&#128472;</div>'+
'</div>';
	document.getElementById("mathCaptcha").innerHTML = mathCaptchaHTML;
	
	var integer = Math.random() * 123456789;
	var fNumber = parseInt(integer.toString().substr(0, 1));
	if(isNaN(fNumber)){fNumber = 3;}
	var lNumber = parseInt(integer.toString().substr(3, 1));
	if(isNaN(lNumber)){lNumber = 7;}
	
	document.getElementById("fNumber").innerHTML = fNumber;
	document.getElementById("lNumber").innerHTML = lNumber;
	document.cookie = "mcValue="+fNumber+lNumber+"; path=/";
}
function checkMathCaptcha(){
	var fNumber = parseInt(document.getElementById("fNumber").innerHTML);
	if(isNaN(fNumber)){fNumber = 0;}
	var lNumber = parseInt(document.getElementById("lNumber").innerHTML);
	if(isNaN(lNumber)){lNumber = 0;}
	var resultN = parseInt(document.getElementById("resultN").value);
	if(isNaN(resultN)){resultN = 0;}
	var expectedResult = parseInt(fNumber+lNumber);
	if(isNaN(expectedResult)){expectedResult = 0;}
	
	if(expectedResult==resultN){
		document.getElementById("resultN").classList.remove("bRed");
		document.getElementById("resultN").classList.add("bGreen");
		return 'Checked';
	}
	else{
		document.getElementById("resultN").classList.remove("bGreen");
		document.getElementById("resultN").classList.add("bRed");
		document.getElementById("resultN").focus();
		return false;
	}
}