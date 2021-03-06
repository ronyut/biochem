function defaultSuccess(msg) {
    if (msg.success) {
        console.log(msg);
    } else {
        console.log("Error adding: " + msg);
        alert("Error adding");
    }
}

// remove tag
$(document).on('itemRemoved', '.tags-input', function(event) {
    let pid = $(this).attr("pid");

    $.ajax({
        method: "POST",
        url: "inc/ajax.php?action=removeTag",
        data: { tid: event.item.tid, pid: pid, text: event.item.name },
        dataType: 'json',
        success: function (msg) {
            if (msg.success) {
                console.log(msg);
            } else {
                console.log("Error removing: " + msg);
                alert("Error removing");
            }
            
        },
        error: function(msg) {
            alert("No response");
        }
    });
});

// Update phrase text
$(document).on("blur", "[contenteditable]", function(e){
    let el = $(this);
    let pid = $(this).attr("pid");
    let md5 = $.md5($(this).text());
    if($(this).attr("hash") == md5) {
        return;
    }

    let is_comment = $(this).hasClass("comment")
    let content = $(this).text();
    if(is_comment) {
        content = $(this).html();
    }

    let commentMarker = "//";
    if (!is_comment && content.includes(commentMarker) && $(".comment[pid="+pid+"]").length > 0) {
        alert("Comment already exists!");
        return;
    }

    $.ajax({
        method: "POST",
        url: "inc/ajax.php?action=updatePhrase",
        data: { text: content,
                pid: $(this).attr("pid"),
                qid: $(this).closest(".article-container").attr("pid"),
                hash: $(this).attr("hash"),
                isComment: is_comment
              },
        dataType: 'json',
        success: function (msg) {
            if (msg.success) {
                console.log($(el).attr("hash"));
                console.log(msg.hash);
                $(el).attr("hash", msg.hash);
            } else {
                console.log("Error updating: " + msg);
                alert("Error updating");
            }
        },
        error: function(msg) {
            alert("No response");
        }
    });
});

// Ctrl click to toggle (in)correct answer
$(document).on('mousedown', '.answer', function (e) {
    let el = $(this);
    let pid = $(this).attr("pid");
    if (e.ctrlKey  &&  e.button === 0) {
        e.stopPropagation ();
        e.preventDefault ()
        
        $.ajax({
            method: "POST",
            url: "inc/ajax.php?action=toggleAnswer",
            data: { pid: pid, text: el.text(), qid: el.closest(".article-container").attr("pid") },
            dataType: 'json',
            success: function (msg) {
                if (msg.success) {
                    if(msg.isCorrect == true) {
                        $(el).parent().removeClass("incorrect").addClass("correct");
                    } else {
                        $(el).parent().removeClass("correct").addClass("incorrect");
                    }
                } else {
                    console.log("Error toggling: " + msg);
                    alert("Error toggling");
                }
            },
            error: function(msg){
                alert("No response");
            }
        });
    }
});

// Remove tag by ctrl+right click
$(document).on('mousedown', '.bootstrap-tagsinput .tag', function (e) {
    if (e.ctrlKey  &&  e.button === 0) {
        let name = $(this).text();
        let input = $(this).closest(".tags-container").find(".tags-input");
        let items = input.tagsinput('items');
        
        var filtered = items.filter(function(el) {
                                 return el.name === name;
                              });
        input.tagsinput('remove', filtered[0]);
    }
});

// toggle question visibility
$(document).on('click', '.toggle-question-visibility', function(event) {
    let qid = $(this).closest(".article-container").attr("pid");
    let el = $(this);

    let toggle = 1;
    if ($(this).attr("toggle") == 1) {
        toggle = 0;
    }

    $.ajax({
        method: "POST",
        url: "inc/ajax.php?action=toggleQuestionVisibility",
        data: { qid: qid, toggle: toggle },
        dataType: 'json',
        success: function (msg) {
            if (msg.success) {
                $(el).attr("toggle", msg.is_hidden);

                if (msg.is_hidden == 1) {
                    $(el).removeClass("fa-eye").addClass("fa-eye-slash");
                }
                else {
                    $(el).removeClass("fa-eye-slash").addClass("fa-eye");
                }
            } else {
                console.log("Error toggling: " + msg);
                alert("Error toggling visibility");
            }
            
        },
        error: function(msg) {
            alert("No response");
        }
    });
});

// add new answer
$(document).on('click', '#new-answer', function(event) {
    let qid = $(this).closest(".article-container").attr("pid");
    let el = $(this);

    $.ajax({
        method: "POST",
        url: "inc/ajax.php?action=addNewAnswer",
        data: { qid: qid },
        dataType: 'json',
        success: function (msg) {
            if (msg.success) {
                let html = `<h2 class="incorrect"><li><span class="answer phrase" contenteditable="true" pid="`+msg.pid+`" hash="">ערוך תשובה...</span></li></h2>`;
                $(".article-container ol").append(html);
            } else {
                console.log("Error adding: " + msg);
                alert("Error adding");
            }
            
        },
        error: function(msg) {
            alert("No response");
        }
    });
});

