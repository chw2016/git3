
function IRR(cashFlows, estimatedResult) { 
var a0=document.getElementById("05").innerHTML;
var a1=document.getElementById("15").innerHTML;
var a2=document.getElementById("25").innerHTML;
var a3=document.getElementById("35").innerHTML;
var a4=document.getElementById("45").innerHTML;
var a5=document.getElementById("55").innerHTML;
var a6=document.getElementById("65").innerHTML;
var a7=document.getElementById("75").innerHTML;
var a8=document.getElementById("85").innerHTML;
var a9=document.getElementById("95").innerHTML;
var a10=document.getElementById("105").innerHTML;
var a11=document.getElementById("115").innerHTML;
var a12=document.getElementById("125").innerHTML;
var a13=document.getElementById("135").innerHTML;
var a14=document.getElementById("145").innerHTML;
var a15=document.getElementById("155").innerHTML;
var a16=document.getElementById("165").innerHTML;
var a17=document.getElementById("175").innerHTML;
var a18=document.getElementById("185").innerHTML;
var a19=document.getElementById("195").innerHTML;
var a20=document.getElementById("205").innerHTML;
var a21=document.getElementById("215").innerHTML;
var a22=document.getElementById("225").innerHTML;
var a23=document.getElementById("235").innerHTML;
var a24=document.getElementById("245").innerHTML;
var a25=document.getElementById("255").innerHTML;

var cashFlows=[parseFloat(a0),a1,a2,a3,a4,a5,a6,a7,a8,a9,a10,a11,a12,a13,a14,a15,a16,a17,a18,a19,a20,a21,a22,a23,a24,a25];
var estimatedResult=0.1;
    var result = "isNAN";  
    if (cashFlows != null && cashFlows.length > 0) { 
	  
        // check if business startup costs is not zero:  
        if (cashFlows[0] != 0) {  
            var noOfCashFlows = cashFlows.length;  
            var sumCashFlows = 0;  
            // check if at least 1 positive and 1 negative cash flow exists:  
            var noOfNegativeCashFlows = 0;  
            var noOfPositiveCashFlows = 0;  
            for (var i = 0; i < noOfCashFlows; i++) {  
                sumCashFlows += cashFlows[i];  
                if (cashFlows[i] > 0) {  
                    noOfPositiveCashFlows++;  
                } else {  
                    if (cashFlows[i] < 0) {  
                        noOfNegativeCashFlows++;  
                    }  
                }  
            }  
  
            // at least 1 negative and 1 positive cash flow available?  
            if (noOfNegativeCashFlows > 0 && noOfPositiveCashFlows > 0) {  
                // set estimated result:  
                var irrGuess = 0.1; // default: 10%  
                if (!isNaN(estimatedResult)) {  
                    irrGuess = estimatedResult;  
                    if (irrGuess <= 0) {  
                        irrGuess = 0.5;  
                    }  
                }  
  
                // initialize first IRR with estimated result:  
                var irr = 0;  
                if (sumCashFlows < 0) { // sum of cash flows negative?  
                    irr = -irrGuess;  
                } else { // sum of cash flows not negative  
                    irr = irrGuess;  
                }  
  
                // iteration:  
                // the smaller the distance, the smaller the interpolation  
                // error  
                var minDistance = 1e-15;  
  
                // business startup costs  
                var cashFlowStart = cashFlows[0];  
                var maxIteration = 100;  
                var wasHi = false;  
                var cashValue = 0;  
                for (var i = 0; i <= maxIteration; i++) {  
                    // calculate cash value with current irr:  
                    cashValue = cashFlowStart; // init with startup costs  
  
                    // for each cash flow  
                    for (var j = 1; j < noOfCashFlows; j++) {  
                        cashValue += cashFlows[j] / Math.pow(1 + irr, j);  
                    }  
  
                    // cash value is nearly zero  
                    if (Math.abs(cashValue) < 0.01) {  
                        result = irr;  
                        break;  
                    }  
  
                    // adjust irr for next iteration:  
                    // cash value > 0 => next irr > current irr  
                    if (cashValue > 0) {  
                        if (wasHi) {  
                            irrGuess /= 2;  
                        }  
                        irr += irrGuess;  
                        if (wasHi) {  
                            irrGuess -= minDistance;  
                            wasHi = false;  
                        }  
                    } else {// cash value < 0 => next irr < current irr  
                        irrGuess /= 2;  
                        irr -= irrGuess;  
                        wasHi = true;  
                    }  
  
                    // estimated result too small to continue => end  
                    // calculation  
                    if (irrGuess <= minDistance) {  
                        result = irr;  
                        break;  
                    }  
                }  
            }  
        }  
    }    
	result = result*100;
	document.getElementById("irr").innerHTML=result.toFixed(2);
}  