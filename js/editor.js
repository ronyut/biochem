// add tag
$('.tags-input').on('beforeItemAdd', function(event) {  
    // dont post tags that are added automatically on page load
    if(event.options && event.options.preventPost) {
        return;
    }
            
    // all items: $(this).tagsinput('items')
    $.ajax({
        method: "POST",
        url: "inc/ajax.php?action=addTag",
        data: { tag: event.item.name, tid: event.item.tid, pid: $(this).attr("pid") },
        dataType: 'json',
        success: function (msg) {
            if (msg.success) {
                console.log(msg);
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

// remove tag
$('.tags-input').on('itemRemoved', function(event) {
    return;
    $.ajax({
        method: "POST",
        url: "inc/ajax.php?action=removeTag",
        data: { tag: event.item.name, pid: $(this).attr("pid") },
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
    if (e.ctrlKey  &&  e.button === 0) {
        e.stopPropagation ();
        e.preventDefault ()
        
        $.ajax({
            method: "POST",
            url: "inc/ajax.php?action=toggleAnswer",
            data: { pid: $(this).attr("pid") },
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


