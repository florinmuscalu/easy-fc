function $(id) { return document.getElementById(id); }

function shuffle(array) {
  var currentIndex = array.length, temporaryValue, randomIndex;

  // While there remain elements to shuffle...
  while (0 !== currentIndex) {

    // Pick a remaining element...
    randomIndex = Math.floor(Math.random() * currentIndex);
    currentIndex -= 1;

    // And swap it with the current element.
    temporaryValue = array[currentIndex];
    array[currentIndex] = array[randomIndex];
    array[randomIndex] = temporaryValue;
  }

  return array;
}

function setCookie(cname, cvalue, exdays) {
  var d = new Date();
  d.setTime(d.getTime() + (exdays*24*60*60*1000));
  var expires = "expires="+ d.toUTCString();
  document.cookie = encodeURIComponent(cname.trim()) + "=" + cvalue + ";" + expires;
}

function getCookie(cname) {
  var name = encodeURIComponent(cname) + "=";
  var ca = document.cookie.split(';');
  for(var i = 0; i < ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}

function fc_setStartBtnText(title, nr_intrebari, cookie_text) {
	return "<span>"+title+"<small> ("+nr_intrebari+" "+fc_options.easyfc_questions_text+")</small></br><small style='color: #444'>"+cookie_text+"</small></span>";
}

function flashcards(id,title) {
	this.id = id;
	this.yScroll = 0;
	this.current = -1; // currently show card
	this.amount = 0; // amount of card in the deck
	this.title = title;
	this.cards = []; // contains the cards; even = question, odd=awnser
	this.correct = new Array(); // Array containing the questions, which have been correctly awnsered (pair 0/1 = question 1, pair 2/3 = question 2, etc.)

	this.fc_update = function(awnser) {
		this.fc_reset_flip();
		// evaluate the given awnser
		if (awnser && this.correct.indexOf(this.current) == -1) this.correct.push(this.current);
		
		this.current++;
	
		// search for the next card which hasn't been awnsered correctly yet
		while (this.correct.indexOf(this.current) > -1) this.current++;
	
		// action to take as the end of the deck has been reached
		if (this.current>this.amount-1) {
			this.current=-1;
			$("fc_main"+this.id).style.display="none";
			$("fc_finish"+this.id).style.display="block";
		
			$("fc_message"+this.id).innerHTML = fc_options.easyfc_correct_answers_text2+" "+((this.amount == this.correct.length)? fc_options.easyfc_correct_all_text:this.correct.length+" "+fc_options.easyfc_questions_total+" "+this.amount+" ("+Math.round((this.correct.length/this.amount)*100)+"%)")+"!";
			if (this.amount == this.correct.length) {
				$("fc_repeat"+this.id).style.display="none";
				$("fc_repeat_test"+this.id).style.display="inline";
			}
			else {
				$("fc_repeat_test"+this.id).style.display="none";
				$("fc_repeat"+this.id).style.display="inline";
			}
		}
	
		// update information which is displayed
		$("fc_content_front"+this.id).innerHTML = "<span style='width: 100%; font-size:"+this.cards[this.current].font_front+"px'>"+this.cards[this.current].q+"</span>";
		$("fc_flip"+this.id).style.height = this.cards[this.current].height+"px";
		setTimeout(() => {  
			$("fc_content_back"+this.id).innerHTML = "<span style='width: 100%; color:black; text-align: -webkit-center; font-size:"+this.cards[this.current].font_back+"px'>"+this.cards[this.current].a+"</span>";
		}, 500);
		$("fc_footer"+this.id).innerHTML = fc_options.easyfc_question_text+" "+(this.current+1)+"/"+this.amount+". "+fc_options.easyfc_correct_answers_text+": "+this.correct.length;
	}

	// continue learning a deck
	this.fc_continue = function() {
		$("fc_start"+this.id).style.display="none";
		$("fc_main"+this.id).style.display="block";
		$("fc_finish"+this.id).style.display="none";
		$("fc_btn_corect"+this.id).style.display="none";
		$("fc_btn_gresit"+this.id).style.display="none";
		this.fc_update(); 
	}
	
	this.fc_reset_flip = function(){
		if ($("fc_btn_corect"+this.id).style.display != "none") {
			$("fc_btn_corect"+this.id).style.display="none";
			$("fc_btn_gresit"+this.id).style.display="none";
			$('fc_content'+this.id).classList.toggle('flipped');
		}
	}
	this.fc_stop = function() {
		setCookie(this.title, this.correct.length, 30);
		$("fc_start_btn"+this.id).innerHTML = fc_setStartBtnText(this.title, this.amount, "Ultima încercare: "+this.correct.length+" răspunsuri corecte.");
		sets.forEach(element => $("fc_start"+element.id).style.display = "block");
		this.fc_reset_flip();
		this.correct = [];
		this.current = -1;
		$("fc_start"+this.id).style.display="block";
		$("fc_main"+this.id).style.display="none";
		$("fc_finish"+this.id).style.display="none";
		window.scroll(0, this.yScroll);
	}
	
	this.fc_start_over = function() {
		this.correct = [];
		this.fc_start();
		//this.fc_update();
	}
	
	// div-display setting when starting a quiz
	this.fc_start = function (){
		this.yScroll = window.scrollY;
		window.scroll(0,0);
		setCookie(this.title, "0", 30);
		sets.forEach(element => $("fc_start"+element.id).style.display = "none");
		this.cards = shuffle(this.cards);
		this.fc_continue();
	}
	
	this.flip = function () {
    	$('fc_content'+this.id).classList.toggle('flipped');
		if ($("fc_btn_corect"+this.id).style.display == "none") {
			$("fc_btn_corect"+this.id).style.display="inline";
			$("fc_btn_gresit"+this.id).style.display="inline";
		} else {
			$("fc_btn_corect"+this.id).style.display="none";
			$("fc_btn_gresit"+this.id).style.display="none";
		}
	}
	
	
	// rename containters for the instance created, set onclick events
	this.fc_setDivs = function (){
		$("fc_start_btn").setAttribute("id", "fc_start_btn"+this.id);
		$("fc_start_btn"+this.id).setAttribute("onclick", "javascript:sets["+this.id+"].fc_start();");
		$("fc_start").setAttribute("id", "fc_start"+this.id);
		$("fc_main").setAttribute("id", "fc_main"+this.id);
		$("fc_finish").setAttribute("id", "fc_finish"+this.id);
		
		$("fc_content").setAttribute("id", "fc_content"+this.id);
		$("fc_content"+this.id).setAttribute("onclick", "javascript:sets["+this.id+"].flip();");
		
		$("fc_flip").setAttribute("id", "fc_flip"+this.id);
		$("fc_content_back").setAttribute("id", "fc_content_back"+this.id);
		$("fc_content_front").setAttribute("id", "fc_content_front"+this.id);
		
		$("fc_footer").setAttribute("id", "fc_footer"+this.id);
		$("fc_message").setAttribute("id", "fc_message"+this.id);
		$("fc_repeat").setAttribute("id", "fc_repeat"+this.id);
		$("fc_repeat_test").setAttribute("id", "fc_repeat_test"+this.id);
		$("fc_btn_corect").setAttribute("id", "fc_btn_corect"+this.id);
		$("fc_btn_corect"+this.id).setAttribute("onclick", "javascript:sets["+this.id+"].fc_update(true);");
		$("fc_btn_gresit").setAttribute("id", "fc_btn_gresit"+this.id);
		$("fc_btn_gresit"+this.id).setAttribute("onclick", "javascript:sets["+this.id+"].fc_update();");
		
		$("fc_repeat_btn").setAttribute("id", "fc_repeat_btn"+this.id);
		$("fc_repeat_btn"+this.id).setAttribute("onclick", "javascript:sets["+this.id+"].fc_continue();");
		
		$("fc_repeat_btn_nu_").setAttribute("id", "fc_repeat_btn_nu_"+this.id);
		$("fc_repeat_btn_nu_"+this.id).setAttribute("onclick", "javascript:sets["+this.id+"].fc_stop();");
		
		$("fc_repeat_btn_nu1_").setAttribute("id", "fc_repeat_btn_nu1_"+this.id);
		$("fc_repeat_btn_nu1_"+this.id).setAttribute("onclick", "javascript:sets["+this.id+"].fc_stop();");
		
		$("fc_btn_reset").setAttribute("id", "fc_btn_reset"+this.id);
		$("fc_btn_reset"+this.id).setAttribute("onclick", "javascript:sets["+this.id+"].fc_stop();");
		
		$("fc_repeat_test_btn").setAttribute("id", "fc_repeat_test_btn"+this.id);
		$("fc_repeat_test_btn"+this.id).setAttribute("onclick", "javascript:sets["+this.id+"].fc_start_over();");
	}
}

instance=-1; // amount of quizzes on current page

var sets = new Array(); // contains the different flashcard-instances