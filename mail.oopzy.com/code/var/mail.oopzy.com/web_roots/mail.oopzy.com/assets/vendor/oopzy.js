function checkStrength(str){

  	  var strength = 0;
      var res = '';
      
      var p = str.charAt(0);
      debug(p);
    	//if it has one special character, increase strength str
       if (p.match(/([!,%,&,@,#,$,^,*,?,_,~])/)){
             return 'invalidfirst'; //invalid first char
       }
       
       if(!isValidEmail(str + '@domain.com'))
   	  {
	   		return 'invalid';
   	  }
      
        //if the password length is less than 6, return message.
        if (str.length < 6) {
      	    res = 'too_short';
      	    return res;
    	}

        //length is ok, lets continue.

    	if (str.length > 5){
        	strength += 1;
    	}
    	

    	//if password contains both lower and uppercase characters, increase strength str
    	if (str.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)){
        	strength += 1;
    	}

    	//if it has numbers and characters, increase strength str
    	if (str.match(/([a-zA-Z])/) && str.match(/([0-9])/)){
        	  strength += 1;
    	}

    	//if it has one special character, increase strength str
        if (str.match(/([!,%,&,@,#,$,^,*,?,_,~])/)){
              strength += 1;
        }

    	//if it has two special characters, increase strength str
        if (str.match(/(.*[!,%,&,@,#,$,^,*,?,_,~].*[!,%,&,@,#,$,^,*,?,_,~])/)){
            strength += 1;
        }

    	//now we have calculated strength str, we can return messages

    	//if str is less than 2
        if (strength < 2 ) {
    		res = 'bad';
    	} else if (strength == 2 ) {
    		res = 'lousy';
    	} else {
    		res = 'good';
    	}

    	return res;
  }

    function isValidEmail(emailAddress)
    {
        var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
        return pattern.test(emailAddress);
    };

    function createCookie(name,value,days)
    {
    	if (days)
        {
    	        var date = new Date();
    	        date.setTime(date.getTime()+(days*24*60*60*1000));
    	        var expires = "; expires="+date.toGMTString();
    	}
    	else var expires = "";
    	document.cookie = escape(name)+"="+escape(value)+expires+"; path=/";
	}

	function readCookie(name)
	{
    	var nameEQ = escape(name) + "=";
    	var ca = document.cookie.split(';');
    	for(var i=0;i < ca.length;i++)
        {
    	    var c = ca[i];
    	    while (c.charAt(0)==' ') c = c.substring(1,c.length);
    	    if (c.indexOf(nameEQ) == 0) return unescape(c.substring(nameEQ.length,c.length));
    	}
    	return '';
	}

	function eraseCookie(name)
	{
	    createCookie(name,"",-1);
	}

