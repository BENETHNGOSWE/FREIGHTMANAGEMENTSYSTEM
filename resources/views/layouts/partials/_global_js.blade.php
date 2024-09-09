<script src="{{ asset('assets/vendor/global/global.min.js') }}"></script>
<script src="{{ asset('assets/js/custom.min.js') }}"></script>
<script src="{{ asset('assets/js/deznav-init.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
<script>
    window.onload = function() {
    var labels = document.getElementsByTagName('label'); // get all labels
    for(var i = 0; i < labels.length; i++) {
        if(labels[i].innerHTML.includes('*')) { // if label text contains *
            labels[i].innerHTML = labels[i].innerHTML.replace('*', '<span class"label" style="color:red;font-size:20px;">*</span>'); // replace * with red *
        }
            }
        };

</script>

<script>
window.onload = function() {
    function updateTime() {
        var now = new Date();
        var day = ("0" + now.getDate()).slice(-2);
        var month = ("0" + (now.getMonth() + 1)).slice(-2);
        var year = now.getFullYear();
        var hour = now.getHours();
        var minute = ("0" + now.getMinutes()).slice(-2);
        var second = ("0" + now.getSeconds()).slice(-2);
        

        var ampm = hour >= 12 ? 'PM' : 'AM';
        hour = hour % 12;
        hour = hour ? hour : 12; 
        hour = ("0" + hour).slice(-2);

        var currentTimeString = day + "-" + month + "-" + year + " | " + hour + ":" + minute + ":" + second + " " + ampm;
        const date = document.getElementById('date_time');
        //check if element exists
        if(date){
            date.innerHTML = currentTimeString;
        }
    }
    setInterval(updateTime, 1000); 
}

</script>

