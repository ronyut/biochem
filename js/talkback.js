$(document).on("click", "[role=tab]", function(e){
    let role = $(this).attr("data");
    if(role == "history") {
        showHistory();
    } else if (role == "talkbacks") {
        showTalkBacks();
    }
    
    
});

function showHistory() {
    let qid = $("body").attr("qid");
    
    $.get("inc/ajax.php?action=getHistory&qid=" + qid, function(data) {
        let i = 0;
        let output = ``;
        $.each(data, function(key, value) {
            let actionKey = this.action.toUpperCase();
            let entity = this.entity.toUpperCase();
            let userID = this.user;
            let fullName = this.userFullName;
            let content = this.content;
            let pid = this.pid;
            let time = this.time;
            
            let action = charToAction(actionKey);
            
            output += `<tr><th scope="row">` + (data.length - (i)) + `</th>`;
            output += `<td>` + time + `</td>`;
            output += "<td>" + fullName + " ";
            
            if(entity == "A") {
                // added
                if (actionKey == "A") {
                    output += "הגדיר/ה את התשובה כנכונה"
                } else {
                    output += "הגדיר/ה את התשובה כלא נכונה"
                }
            } else if (entity == "Q" || pid == qid) {
                output += action + "את השאלה"
            } else if (entity == "P") {
                output += action + "את התשובה"
            } else if (entity == "T") {
                output += action + "את התגית"
            } else if (entity == "U") {
                
            }
            
            output += "</td><td>" + content + "</td></tr>";
            i++;
        });
        if(output != "") {
            $("#history_tbody").html(output);
        }
    });
}

function charToAction(c) {
    let action = "???";
    
    if(c == "A") {
        action = "הוסיפ/ה ";
    } else if (c == "E") {
        action = "ערכ/ה ";
    } else if (c == "D") {
        action = "מחק/ה ";
    }
    
    return action;
}

$("#talkbackForm").submit(function(e) {
    e.preventDefault();

    let talkback = $(this).find("textarea").val();
    if(talkback != "") {
        addTalkback(talkback, null);
    }
});

$(document).on("change keyup", "#talkbackForm textarea", function(e) {
    if($(this).val() == "") {
        $("#talkbackForm button[type=submit]").addClass("disabled");
    } else {
            console.log($(this).val());
       $("#talkbackForm button[type=submit]").removeClass("disabled"); 
    }
});


function addTalkback(talkback, replyTo) {
    let qid = $("body").attr("qid");
    let data = {"qid": qid, "talkback": talkback};
    if(replyTo != null) {
        data["replyToTalkbackID"] = replyTo["talkbackID"];
        data["replyToUserID"] = replyTo["userID"];
    }
    
    $.ajax({
        method: "POST",
        url: "inc/ajax.php?action=addTalkback",
        data: data,
        dataType: 'json',
        cache: false,
        success: function (results) {
            $("#talkbackForm textarea").val("");
            $("#talkbackForm textarea").trigger("change");
            $("a[data=talkbacks]").click();
        },
        error: function(msg) {
            alert("Error fetching data from server");
        }
    });
}

function showTalkBacks() {
    let qid = $("body").attr("qid");
    $.get("inc/ajax.php?action=getTalkbacks&qid=" + qid, function(data) {
        let i = 0;
        let output = ``;
        $.each(data, function(key, value) {
            
            i++;
        });
    });
}