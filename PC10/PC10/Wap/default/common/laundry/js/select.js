 $(function(){
    var calendar = new Date();
        //当前天数
        var today = calendar.getFullYear()+"-"+(calendar.getMonth()+1)+"-"+calendar.getDate();
        if(today.substr(-2).indexOf("-") != -1){
            today = calendar.getFullYear()+"-"+(calendar.getMonth()+1)+"-0"+calendar.getDate();
        }
        //当前月的最后一天
        var adjustday = calendar.getFullYear()+"-"+(calendar.getMonth()+1)+"-"+getDays();
        // alert(adjustday);
        var today_o = document.createElement("option");
        today_o.setAttribute("value",today);
        //today_o.setAttribute("selected",'selected');
        today_o.innerHTML = today;
        document.getElementById("washing_date").appendChild(today_o);
        var later = new Array();
        var later_o = new Array();
        
        for (var i = 1;i <= 6; i++) {
            //这个地方要去解决月份以及年份的问题
            later[i] = calendar.getFullYear()+"-"+(calendar.getMonth()+1)+"-"+(calendar.getDate()+i);

            if(String(later[i]).substr(-2).indexOf("-") == "-1"){
               if(String(later[i]).substr(-2) > getDays()){
                 //这里假设时间为2014-12-26,解决年份问题
                    if(String(later[i]).substr(-5,2) == "12" && String(later[i]).substr(-2) > getDays()) {
                       later[i] = (calendar.getFullYear()+1)+"-1-0"+(calendar.getDate()+i-getDays());
                    }else{
                       later[i] = calendar.getFullYear()+"-"+(calendar.getMonth()+2)+"-0"+(calendar.getDate()+i-getDays());
                    }      
                    
                }else{
                        later[i] = calendar.getFullYear()+"-"+(calendar.getMonth()+1)+"-"+(calendar.getDate()+i); 
                }
                
            }else{
                later[i] = calendar.getFullYear()+"-"+(calendar.getMonth()+1)+"-"+"0"+(calendar.getDate()+i);  
            }
            later_o[i] = document.createElement("option");
            later_o[i].setAttribute("value",later[i]);
            later_o[i].innerHTML = later[i];
            document.getElementById("washing_date").appendChild(later_o[i]); 
        };

    //时间段代码
            var currentTime;
            var timeline = new Array();
            var front;
            var last;
            var currentHours = parseInt((24-(calendar.getHours()+1))/2);
        
            $('#washing_date').change(function(){

                currentTime = $(this).val();
                //删除节点
                if(timeline.length != 0){
                    for(var j= timeline.length-1;j>=0;j--){
                        
                        timeline[j].remove();
                    }
                }
                
                // if(currentTime.substr(5,2).indexOf('-') == -1){
                    if(currentTime.substr(-2,2) - calendar.getDate() > 0){
                        for(var i = 0;i<=8;i++){
                            front = 6+2*i;
                            last = 8+2*i;
                            timeline[i] = document.createElement('option');
                            timeline[i].setAttribute('value',front+":00 - "+last+":00");
                            timeline[i].innerHTML = front+":00 - "+last+":00";
                            document.getElementById('washing_time').appendChild(timeline[i]);
                        }
                    }else{
                        for(var i =0;i<currentHours;i++){
                            timeline[i] = document.createElement('option');
                            if(calendar.getHours()%2 == 0){
                                front = calendar.getHours()+2+2*i;
                                last = calendar.getHours()+4+2*i;
                                timeline[i].setAttribute('value',front+":00 - "+last+":00");
                                timeline[i].innerHTML = front+":00 - "+last+":00";
                                document.getElementById('washing_time').appendChild(timeline[i]);
                            }else{
                                front = calendar.getHours()+1+2*i;
                                last = calendar.getHours()+3+2*i;
                                timeline[i].setAttribute('value',front+":00 - "+last+":00");
                                timeline[i].innerHTML = front+":00 - "+last+":00";
                                document.getElementById('washing_time').appendChild(timeline[i]);
                            }
                            
                        }
                    }
                    //这种移除子节点的方式不正确
                    
                    
                // }
            })  

        //返回某月的总天数
        function getDays(){
            var date = new Date();
            var y = date.getFullYear();
            var m = date.getMonth() + 1;
            if(m == 2){
                return y % 4 == 0 ? 29 : 28;
            }else if(m == 1 || m == 3 || m == 5 || m == 7 || m == 8 || m == 10 || m == 12){
                return 31;
            }else{
                return 30;
            }
        }
 })
        

    
