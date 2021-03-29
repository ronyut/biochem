let qid = $("body").attr("qid");
let logged = $("body").attr("userID");
showTalkBacks();

let MAIN_TB_TEMPLATE = `<li class="media">
                            <div class="talkback thread" threadID="{{THREAD_ID}}" tbid="{{ID}}" userID="{{USER_ID}}">
                                <div class="pull-right">
                                    <span class="media-count reviews hidden">{{INDEX}}</span>
                                    <a>
                                      <img class="media-object img-circle" src="{{IMAGE}}" alt="profile">
                                    </a>
                                </div>
                                <div class="media-body">
                                  <div class="well well-lg rtl">
                                    <div class="row">
                                        <div class="media-title col-xs-12">
                                            <div class="media-date">{{DATE}}</div>
                                            <h4 class="media-name reviews">{{NAME}}</h4>
                                        </div>
                                        <div class="col-xs-12">
                                          <button type="button" class="vote likeButton">
                                              {{LIKE_BUTTON}}
                                              <span data-test="likeCount">0</span>
                                          </button>
                                          <button type="button" class="vote dislikeButton">
                                              {{DISLIKE_BUTTON}}
                                              <span data-test="dislikeCount">0</span>
                                          </button>
                                        </div>
                                        <div class="col-xs-12">
                                            <div class="media-talkback textarea" dir="auto">{{MESSAGE}}</div>
                                        </div>
                                        <div class="col-xs-12">
                                            <button class="btn btn-info btn-circle reply-btn"><span class="glyphicon glyphicon-share-alt"></span> הגב</button>
                                            <button class="btn btn-warning btn-circle" data-toggle="collapse" href="#replies_to_{{ID}}"><span class="glyphicon glyphicon-comment"></span> <span class="replies-counter">אין</span> תגובות</button>
                                            <button class="btn btn-default btn-circle edit-btn hidden"><span class="glyphicon glyphicon-pencil"></span> <span class="text">ערוך</span></button>
                                            <div class="toggleNewReply hidden">
                                                <br>
                                                <div contenteditable="true" dir='rtl' class='textarea temp-textarea-reply form-control'></div>
                                                <br>
                                                <button class="btn btn-success btn-circle reply-btn-send disabled"><span class="glyphicon glyphicon-send"></span> פרסמ/י תגובה</button>
                                            </div>
                                        </div>
                                    </div>
                                  </div>              
                                </div>
                                <div class="collapse in replies" id="replies_to_{{ID}}">
                                    <ul class="media-list" id="replies"></ul>  
                                </div>
                            </div>
                          </li>`;
                          
let REPLY_TEMPLATE = `<li class="media media-replied">
                        <div class="talkback reply" tbid="{{ID}}" userID="{{USER_ID}}">
                            <div class="pull-right media-head media-replied">
                                <a href="#">
                                  <img class="media-object img-circle" src="{{IMAGE}}" alt="profile">
                                </a>
                            </div>
                            <div class="media-body">
                              <div class="well well-lg rtl">
                                  <div class="row">
                                    <div class="col-xs-12">
                                        <div class="media-title">
                                            <div class="arrow-replyTo">
                                                <span>{{REPLY_TO_NAME}}</span>
                                                {{REPLY_TO_ARROW}}
                                            </div>
                                            <div class="media-date">{{DATE}}</div>
                                            <h4 class="media-name reviews">{{NAME}}</h4>
                                        </div>
                                    </div>
                                    <div class="col-xs-12">
                                      <button type="button" class="vote likeButton">
                                            {{LIKE_BUTTON}}
                                          <span data-test="likeCount">0</span>
                                      </button>
                                      <button type="button" class="vote dislikeButton">
                                            {{DISLIKE_BUTTON}}
                                          <span data-test="dislikeCount">0</span>
                                      </button>
                                    </div>
                                    <div class="col-xs-12">
                                        <div class="media-talkback textarea" dir="auto">{{MESSAGE}}</div>
                                    </div>
                                    <div class="col-xs-12">
                                          <button class="btn btn-info btn-circle reply-btn"><span class="glyphicon glyphicon-share-alt"></span> הגב</button>
                                          <button class="btn btn-default btn-circle edit-btn hidden"><span class="glyphicon glyphicon-pencil"></span> <span class="text">ערוך</span></button>
                                          <div class="toggleNewReply hidden">
                                              <br> 
                                              <div contenteditable="true" dir='rtl' class='temp-textarea-reply textarea form-control'></div>
                                              <br>
                                              <button class="btn btn-success btn-circle reply-btn-send disabled"><span class="glyphicon glyphicon-send"></span> פרסמ/י תגובה</button>
                                          </div>
                                    </div>
                                 </div>
                              </div>              
                            </div>
                        </div>
                    </li>`;

$(document).on("click", "[role=tab]", function(e){
    let role = $(this).attr("data");
    if(role == "history") {
        showHistory();
    } else if (role == "talkbacks") {
        showTalkBacks();
    }
});

function showHistory() {
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
            } else if (entity == "C") {
                output += action + "את ההערה"
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
    } else if (c == "H") {
        action = "הסתיר/ה ";
    } else if (c == "S") {
        action = "חשפ/ה ";
    }
    
    return action;
}

$("#talkbackForm").submit(function(e) {
    e.preventDefault();

    let talkback = $(this).find(".textarea").html();
    if(talkback != "") {
        addTalkback(talkback, null, function(){
            $("#talkbackForm textarea").val("");
            $("#talkbackForm textarea").trigger("change");
        });
    }
});

$(document).on("change keyup", "#talkbackForm .textarea", function(e) {
    if($(this).html() == "") {
        $("#talkbackForm button[type=submit]").addClass("disabled");
    } else {
       $("#talkbackForm button[type=submit]").removeClass("disabled"); 
    }
});


function addTalkback(talkback, replyTo, callback) {
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
        success: function(data) {
            callback();
            repliesDataToHtml();
        },
        error: function(msg) {
            alert("Error fetching data from server");
        }
    });
}

function showTalkBacks(scrollTo = null) {
    $.get("inc/ajax.php?action=getTalkbacks&qid=" + qid, function(data) {
        $("ul#threads").html("");
        repliesDataToHtml(data, scrollTo);
        
        var counter = data.reduce(function(obj, v) {
          // increment or set the property
          // `(obj[v.status] || 0)` returns the property value if defined
          // or 0 ( since `undefined` is a falsy value
          obj[v.underTalkback] = (obj[v.underTalkback] || 0) + 1;
          return obj;
        }, {});
        
        $.each(counter, function(key, value) {
            if (key != null) {
                $(".thread[tbid="+key+"] .replies-counter").text(value);
            }
        });
    });
}

function repliesDataToHtml(data, scrollTo){
    $.each(data, function(key, value) {
        let tbid = this.id;
        let msg = this.msg;
        let photo = this.photo;
        let time = this.time;
        let userID = this.userID;
        let userFullName = this.userFullName;
        let underTalkback = this.underTalkback;
        let replyToUserName = this.replyToUserName;
        
        let like = $("#templates #likeButton").get(0).outerHTML;
        let dislike = $("#templates #dislikeButton").get(0).outerHTML;
        let arrow = $("#templates #replyToArrow").get(0).outerHTML;
        
        let replacements = {"ID": tbid, "NAME": userFullName, "MESSAGE": msg, "DATE": time, "LIKE_BUTTON": like, "DISLIKE_BUTTON": dislike, "REPLY_TO_NAME": replyToUserName, "REPLY_TO_ARROW": arrow, "IMAGE": photo,
        "USER_ID": userID};
        
        let template;
        // main talkback
        if (underTalkback == null) {
            template = MAIN_TB_TEMPLATE;
            replacements["THREAD_ID"] = tbid;
        }
        else {
            template = REPLY_TEMPLATE;
            replacements["THREAD_ID"] = underTalkback;
        }
        
        $.each(replacements, function(key, value) {
            let regex = new RegExp("\{\{" + key + "\}\}", "g");
            template = template.replace(regex, value); 
        });
        
        if (underTalkback == null) {
            $("ul#threads").append(template);
        } else {
            $(".thread[tbid="+underTalkback+"] .replies ul").prepend(template);
        }
        
        if(logged == userID) {
            $(".talkback[tbid="+tbid+"] .edit-btn").removeClass("hidden");
            $(".talkback[tbid="+tbid+"] .reply-btn").addClass("hidden");
        }
    });
    
    // scroll to new talkback
     if(scrollTo != null) {
        $([document.documentElement, document.body]).animate({
            scrollTop: $(".talkback[tbid="+scrollTo+"]").offset().top
        }, 500);
    }
}

/*
    Show textarea of sub talkback once clicked on the button
*/
$(document).on("click", ".reply-btn", function(e) {
    $(this).parent().find(".toggleNewReply").toggleClass("hidden");
});

/*
    Disable send button if talkback textarea is empty
*/
$(document).on("change keyup", ".toggleNewReply .temp-textarea-reply", function(e) {
    let btn = $(this).parent().find(".reply-btn-send");
    if($(this).html() == "") {
        btn.addClass("disabled");
    } else {
       btn.removeClass("disabled");
    }
});

/*
    Add new sub talkback
*/
$(document).on("click", ".reply-btn-send", function(e) {
    let replyTo = {};
    replyTo["talkbackID"] = $(this).closest(".talkback[threadID]").attr("threadID");
    replyTo["userID"] = $(this).closest(".talkback").attr("userID");
    let textarea = $(this).parent().find(".temp-textarea-reply");
    
    addTalkback(textarea.html(), replyTo, function() {
        textarea.html("");
    });
});

/*
    Edit talkback
*/
$(document).on("click", ".edit-btn", function(e) {
    let tb = $(this).parent().parent().find(".media-talkback");
    let tbid = $(this).closest(".talkback[tbid]").attr("tbid");

    if(tb.attr("contenteditable")) {
        tb.removeAttr("contenteditable");
        $(this).find("span.text").text("ערוך");
        updateTalkback(tb, tbid);
    } else {
        tb.attr("contenteditable", "true");
        $(this).find("span.text").text("סיים עריכה");
    }
});

/*
    Edit talkback - ajax
*/
function updateTalkback(tb, tbid) {
    $.ajax({
        method: "POST",
        url: "inc/ajax.php?action=updateTalkback",
        data: { talkback: $(tb).html(), tbid: tbid },
        dataType: 'json',
        cache: false,
        success: function (data) {
            if (!data.success) {
                alert("Error");
                console.log(data);
            }
        },
        error: function(msg) {
            alert("Error fetching data from server");
        }
    });
}