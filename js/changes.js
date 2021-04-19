var qids = [];
var users = {};

let MAIN_POST_TEMPLATE = `<div class="main-post" hasbody="0">
							<header>
								<a href="item.php?id={{QID}}" target="_blank">שאלה #{{QID}}</a>
								<small class="details">נוספה בתאריך <span class="added_time">{{DATE_ADDED}}</span> ע"י <span class="adder_name">{{ADDER_NAME}}</span></small>
							</header>
							<div class="main-post-body">{{QUESTION_BODY}}</div>
						</div>
						<div class="sub-posts"></div>
						<div class="show-more" show="0">הצג עוד...</div>`;
                          
let SUB_POST_TEMPLATE = `<div class="sub-post {{CLASSES}}">
							<div class="wrap-sub-post">
								<header>
									<img uid="{{USER_ID}}" src="{{USER_IMAGE}}" alt="{{COMMENTER}}" width="40" height="40" class="img-responsive img-circle img-thumbnail">
									<small class="commenter">{{COMMENTER}}</small>
									<small class="details">{{DATE_EDITED}}</small>
								</header>
								<div class="sub-post-body">{{SUBPOST_BODY}}</div>
							</div>
						</div>`;

function showHistory() {
    $.get("inc/ajax.php?action=getAllHistory", function(data) {
        let output = ``;
        $.each(data, function(key, value) {
            let actionKey = this.action.toUpperCase();
            let entity = this.entity.toUpperCase();
            let userID = this.userID;
            let fullName = this.userFullName;
            let content = this.content;
            let pid = this.pid;
            let time = this.time;
            let qid = this.qid;
			
			let action = getActionText(actionKey, entity, pid, qid);
			
			// add main post
			if (!qids.includes(qid)) {
				let template = `<article qid="`+qid+`">` + MAIN_POST_TEMPLATE + `</article>`;
				template = template.replaceAll("{{QID}}", qid);
				$("#articles").append(template);
				qids.push(qid);
			}
			
			if (entity == "Q" && (actionKey == "A" || actionKey == "E")) {
				let mainpost = $("article[qid="+qid+"] .main-post");
				if (mainpost.attr("hasbody") == 0) {
					mainpost.find(".main-post-body").html(content);
					mainpost.attr("hasbody", 1);
				}
				
				if (actionKey == "A") {
					mainpost.find(".adder_name").text(fullName);
					mainpost.find(".added_time").text(time);
				}
				
			}
			
			let template = SUB_POST_TEMPLATE;
			let replacements = {"COMMENTER": fullName, "DATE_EDITED": time,
								"SUBPOST_BODY": action + ": " + content, "USER_ID": userID,
								"USER_IMAGE": users[userID]};
											
			$.each(replacements, function(key, value) {
				let regex = new RegExp("\{\{" + key + "\}\}", "g");
				template = template.replace(regex, value); 
			});
			
			let subposts = $("article[qid="+qid+"] .sub-post");
			
			if (subposts.length >= 3) {
				if (subposts.length >= 3) {
					$("article[qid="+qid+"] .show-more").show();
				}
				
				template = template.replaceAll("{{CLASSES}}", "after-3");
			} else {
				template = template.replaceAll("{{CLASSES}}", "");
			}
			
			$("article[qid="+qid+"] .sub-posts").append(template);
        });
		
		$(".loader").hide();
    });
}


function getActionText(actionKey, entity, pid, qid) {
	let output = "";
	let action = charToAction(actionKey);
	
	if(entity == "A") {
		// added
		if (actionKey == "A") {
			output += "הגדיר/ה את התשובה כנכונה"
		} else {
			output += "הגדיר/ה את התשובה כלא נכונה"
		}
	} else if (entity == "Q") {
		output += action + "את השאלה"
	} else if (entity == "P") {
		output += action + "את התשובה"
	} else if (entity == "T") {
		output += action + "את התגית"
	} else if (entity == "C") {
		output += action + "את ההערה"
	}
	
	return output;
}


function charToAction(c) {
    let action = "???";
    
    if(c == "A") {
        action = "הוסיפ/ה ";
    } else if (c == "E") {
        action = "ערכ/ה ";
    } else if (c == "D") {
        action = "מחק/ה ";
    } else if (c == "H") {
        action = "הסתיר/ה ";
    } else if (c == "S") {
        action = "חשפ/ה ";
    }
    
    return action;
}


/*
    toggle visibility of hidden sub posts
*/
$(document).on('click', '.show-more', function (e) {
	let after3 = $(this).parent().find(".after-3");
    if ($(this).attr("show") == 0) {
		after3.show();
		$(this).text("הצג פחות...");
		$(this).attr("show", 1);
	} else {
		after3.hide();
		$(this).text("הצג יותר...");
		$(this).attr("show", 0);
	}
});

function getUsersPhotos() {
    $.get("inc/ajax.php?action=getUsersPhotos", function(data) {
		$.each(data, function(key, value) {
			users[this.uid] = this.img;
		});
		
		showHistory();
	});
}