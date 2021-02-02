function toggleField(hideObj,showObj) {
  hideObj.disabled=true;
  hideObj.style.display='none';
  showObj.disabled=false;
  showObj.style.display='inline';
  showObj.focus();
}
function checkPass(){                   //used in confirm matching password entries
  var pass1 = document.getElementById('pass1');
  var pass2 = document.getElementById('pass2');
  var goodColor = "#66cc66";
  var badColor = "#ff6666";
  if((pass1.value != '') && (pass1.value == pass2.value)){
    pass2.style.backgroundColor = goodColor;
    document.getElementById('submitpwd').removeAttribute("disabled");
  }else{
    pass2.style.backgroundColor = badColor;
    document.getElementById('submitpwd').setAttribute("disabled","disabled");
  }
}
function enableOnNonEmpty() {
    if (arguments.length >= 2)
    {
	var ti = document.getElementById(arguments[0]);
	if(ti.value.replace(/ /g,'') != '') {
	    for (i = 1; i < arguments.length; i++) {
		document.getElementById(arguments[i]).removeAttribute("disabled");
	    }
	}
	else {
	    for (i = 1; i < arguments.length; i++) {
		document.getElementById(arguments[i]).setAttribute("disabled", "disabled");
	    }
	}
    }
}
function checkPsk() {
    if(psk1.value.length > 0 && psk1.value.length < 8) {
	psk1.style.background='#ff6666';
    } else {
	psk1.style.background='#66cc66';
    }
}
function checkPskMatch(){                   //used in confirm matching psk entries
    var psk1 = document.getElementById('psk1');
    var psk2 = document.getElementById('psk2');
    var goodColor = "#66cc66";
    var badColor = "#ff6666";
    if((psk1.value != '') && (psk1.value == psk2.value)){
	psk2.style.backgroundColor = goodColor;
	document.getElementById('submitpsk').removeAttribute("disabled");
    }else{
	psk2.style.backgroundColor = badColor;
	document.getElementById('submitpsk').setAttribute("disabled","disabled");
    }
}
function checkFrequency(){
  // Set the colours
  var goodColor = "#66cc66";
  var badColor = "#ff6666";
  // Get the objects from the config page
  var freqTRX = document.getElementById('confFREQ');
  var freqRX = document.getElementById('confFREQrx');
  var freqTX = document.getElementById('confFREQtx');
  var freqPOCSAG = document.getElementById('pocsagFrequency');
  if(freqTRX){
    confFREQ.style.backgroundColor = badColor;		// Set to bad colour first, then check
    var intFreqTRX = parseFloat(freqTRX.value);		// Swap to float
    // TRX Good
    if (144 <= intFreqTRX && intFreqTRX <= 148)   { confFREQ.style.backgroundColor = goodColor; }
    if (220 <= intFreqTRX && intFreqTRX <= 225)   { confFREQ.style.backgroundColor = goodColor; }
    if (420 <= intFreqTRX && intFreqTRX <= 450)   { confFREQ.style.backgroundColor = goodColor; }
    if (842 <= intFreqTRX && intFreqTRX <= 950)   { confFREQ.style.backgroundColor = goodColor; }
    // TRX Bad
    if (145.8 <= intFreqTRX && intFreqTRX <= 146) { confFREQ.style.backgroundColor = badColor; }
    if (435 <= intFreqTRX && intFreqTRX <= 438)   { confFREQ.style.backgroundColor = badColor; }
  }
  if(freqRX){
    confFREQrx.style.backgroundColor = badColor;	// Set to bad colour first, then check
    var intFreqRX = parseFloat(freqRX.value);		// Swap to float
    // RX Good
    if (144 <= intFreqRX && intFreqRX <= 148)   { confFREQrx.style.backgroundColor = goodColor; }
    if (220 <= intFreqRX && intFreqRX <= 225)   { confFREQrx.style.backgroundColor = goodColor; }
    if (420 <= intFreqRX && intFreqRX <= 450)   { confFREQrx.style.backgroundColor = goodColor; }
    if (842 <= intFreqRX && intFreqRX <= 950)   { confFREQrx.style.backgroundColor = goodColor; }
    // RX Bad
    if (145.8 <= intFreqRX && intFreqRX <= 146) { confFREQrx.style.backgroundColor = badColor; }
    if (435 <= intFreqRX && intFreqRX <= 438)   { confFREQrx.style.backgroundColor = badColor; }
  }
  if(freqTX){
    confFREQtx.style.backgroundColor = badColor;	// Set to bad colour first, then check
    var intFreqTX = parseFloat(freqTX.value);		// Swap to float
    // TX Good
    if (144 <= intFreqTX && intFreqTX <= 148)   { confFREQtx.style.backgroundColor = goodColor; }
    if (220 <= intFreqTX && intFreqTX <= 225)   { confFREQtx.style.backgroundColor = goodColor; }
    if (420 <= intFreqTX && intFreqTX <= 450)   { confFREQtx.style.backgroundColor = goodColor; }
    if (842 <= intFreqTX && intFreqTX <= 950)   { confFREQtx.style.backgroundColor = goodColor; }
    // TX Bad
    if (145.8 <= intFreqTX && intFreqTX <= 146) { confFREQtx.style.backgroundColor = badColor; }
    if (435 <= intFreqTX && intFreqTX <= 438)   { confFREQtx.style.backgroundColor = badColor; }
  }
  if(freqPOCSAG){
    pocsagFrequency.style.backgroundColor = badColor;		// Set to bad colour first, then check
    var intFreqPOCSAG = parseFloat(freqPOCSAG.value);		// Swap to float
    // TX Good
    if (144 <= intFreqPOCSAG && intFreqPOCSAG <= 148)   { pocsagFrequency.style.backgroundColor = goodColor; }
    if (220 <= intFreqPOCSAG && intFreqPOCSAG <= 225)   { pocsagFrequency.style.backgroundColor = goodColor; }
    if (420 <= intFreqPOCSAG && intFreqPOCSAG <= 450)   { pocsagFrequency.style.backgroundColor = goodColor; }
    if (842 <= intFreqPOCSAG && intFreqPOCSAG <= 950)   { pocsagFrequency.style.backgroundColor = goodColor; }
    // TX Bad
    if (145.8 <= intFreqPOCSAG && intFreqPOCSAG <= 146) { pocsagFrequency.style.backgroundColor = badColor; }
    if (435 <= intFreqPOCSAG && intFreqPOCSAG <= 438)   { pocsagFrequency.style.backgroundColor = badColor; }
  }
}
