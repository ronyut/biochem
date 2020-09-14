/*
    Global vars
*/
let LIMIT_TAGS = false;
let MAX_VISIBLE_TAGS = 30;

init();

/*
    init on page load
*/
function init(){
    refreshCount();
}

/*
    count results based on filters
*/
function refreshCount() {
    let cnt = $(".article-container[show=true]").length;
    $("#cnt_visible").text(cnt);
}

/*
    toggle limiting number of maximum visible tags
*/
$(document).on('click', '#showAllTags', function (e) {
    LIMIT_TAGS = !LIMIT_TAGS;
});

/*
    Filtering
*/
$(document).on('click', '.tag-link', function (e) {
    let name = $(this).text();
    let tid = $(this).attr("tid");
    let letsDisable = $(this).hasClass("tag-active");
    
    $(this).toggleClass("tag-active");
    
    
    $(".tags-input").each(function(i, el){
        let tags = $(el).tagsinput('items');        
        let container = $(el).closest(".article-container");
        
        // if tid not found in tags of this question 
        if (typeof tags === 'object' && !tags.some(e => e.tid == tid)) {
             if(!letsDisable) {
                container.attr("show", "false"); // not found id + we want to enable filter  -> bye bye
            } else {
                let arr = [];
                for (let tag in tags){
                    arr.push(tags[tag].tid);
                }
                
                if(letsDisable) {
                    let filters = getActiveFilters();
                    let count = 0;
                    for (let filter in filters){
                        if (arr.includes(parseInt(filters[filter]))) {
                            count += 1;
                        }
                    }
                    
                    if(count == filters.length) {
                        container.attr("show", "true"); // not found id + want to disable -> show
                    } else {
                        container.attr("show", "false");
                    }
                }
            }
        }
    });
    
    validate(tid);
    refreshCount();
});

/*
    getActiveFilters
*/
function getActiveFilters() {
    let filters = $(".tag-active");
    let ids = [];
    filters.each(function(i, el){
        ids.push($(el).attr("tid"));
    });
    
    return ids;
}

/*
    getInactiveFilters
*/
function getInactiveFilters() {
    let filters = $(".tag-link:not(.tag-active)");
    let ids = [];
    filters.each(function(i, el){
        ids.push($(el).attr("tid"));
    });
    
    return ids;
}

/*
    Validate tags up in the filtering area:
    i.e. if we already applied a Y filter and the tag X on top doesn't appear in any filtered question - hide it.
    and vice versa: if we removed that Y filter and the tag X now appears in a filtered question - show X on top again.
*/
function validate(tid) {
    let inactive = getInactiveFilters();
    let tags = $(".article-container[show=true] .tags-input").tagsinput('items');
    let newArr = [];

    if ($(".article-container[show=true]").length > 1){
        // convert object of arrays of objects to: array of objects
        tags.forEach(arr => {
            for (let obj in arr){
                newArr.push(arr[obj]);
            }
        });
    } else {
        newArr = tags;
    }
    
    let i = 0;
    for (let filter in inactive){
        let fil = parseInt(inactive[filter]);

        if (typeof newArr === 'object'){
            if (!newArr.some(e => e.tid == fil)) {
                $(".tag-link[tid="+fil+"]").addClass("hidden");
            } else {
                $(".tag-link[tid="+fil+"]").removeClass("hidden");
            }
            
        }
        
        if(LIMIT_TAGS){
            if (i > MAX_VISIBLE_TAGS && $(".tag-link:not(.tag-active):not(.hidden)").length > MAX_VISIBLE_TAGS) {
                $(".tag-link[tid="+fil+"]").addClass("hidden");
            }
        }
        
        i++;
    }
}

/*
    Clear results
*/
function clearMyAlgoResults() {
    $(".myAlgo-results").html("");
    $(".dropdown-results").hide();
    $(".highlight").contents().unwrap();
}

/*
    Search
*/
$("#myAlgoForm").submit(function(e) {
    // prevent page refreshing
    e.preventDefault();
    
    let query = $("#myAlgo").val();
    clearMyAlgoResults();

    if(query == "") {
        return;
    }
    
    // Remove all filters and show all questions
    $(".tag-link").removeClass("tag-active").removeClass("hidden");
    $(".article-container[show=false]").attr("show", true);
    
    $.ajax({
        method: "POST",
        url: "inc/find.php",
        data: { query: $("#myAlgo").val() },
        dataType: 'json',
        cache: false,
        success: function (results) {
            let notEmpty = false;
            let output = "";
            let i = 1;
            for(let res in results) {
                let id = results[res].id;
                let score = results[res].score;
                if(score == 0) {
                    continue;
                }
                
                let highlights = results[res].highlight;
                $(".article-container[pid="+id+"] .phrase").each(function(i, el){
                    let html = $(el).html();
                    for(let high in highlights) {
                        let hl = highlights[high];
                        if(html.includes(hl)) {
                            html = replaceAll(html, hl, "<span class='highlight'>"+hl+"</span>");
                        }
                    }
                    $(el).html(html);
                });
                output += "<a href='#' class='scroll-to dropdown-item' pid='"+id+"'><h"+i+">תוצאה "+i+"</h"+i+"></a>";
                notEmpty = true;
                i++;
            }
            
            if(!notEmpty) {
                output += "<a href='#' class='dropdown-item'><h1>אין תוצאות</h1></a>";
            }
            
            $(".myAlgo-results").html(output);
            $(".dropdown-results").show();
            // scroll to result
            $(".scroll-to:first").click();
        },
        error: function(msg) {
            alert("Error fetching data from server");
        }
    });
});

/*
    Scroll to the question when clicking on a result in the search window
*/
$(document).on('click', '.scroll-to', function (e) {
    let id = $(this).attr("pid");
    $([document.documentElement, document.body]).animate({
        scrollTop: $(".article-container[pid="+id+"]").offset().top - 80
    }, 250);
});

/*
    escape RegExp
*/
function escapeRegExp(string) {
    return string.replace(/[.*+\-?^${}()|[\]\\]/gi, '\\$&'); // $& means the whole matched string
}

/*
    replace all occurences of a substring in a string
*/
function replaceAll(str, find, replace) {
    return str.replace(new RegExp(escapeRegExp(find), 'gi'), replace);
}

/*
    enable tags
*/
let tags = new Bloodhound({
  datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
  queryTokenizer: Bloodhound.tokenizers.whitespace,
  identify: function (obj) { return obj.name; },
  remote: {
    url: 'inc/tags.php?tag=%QUERY',
    wildcard: '%QUERY',
    filter: function (data) {
                if (data) {
                    return $.map(data, function (object) {
                        return { tid: object.tid, name: object.name };
                    });
                } else {
                    return {};
                }
            },
    rateLimitBy: 'debounce',
    rateLimitWait: 400
  }
});
tags.initialize();

$('.tags-input').tagsinput({
    tagClass: function(item) {
        switch (item.approved) {
            case false : return 'tag-unapproved';
        }
    },
    itemValue: 'tid',
    itemText: 'name',
    typeaheadjs: {
        name: 'tags',
        displayKey: 'name',
        keyValue: 'tid',
        source: tags.ttAdapter()
    },
    limit: 100,
    trimValue: true,
    supermode: true
});

/*
    Fetch the tags
*/

let input = $(".tags-input");
let qid = null;
if(input.length == 1){
    qid = input.attr("pid");
}

$.ajax({
    method: "GET",
    url: "inc/ajax.php?action=getTagsDataForJS",
    dataType: 'json',
    data: {qid: qid},
    cache: false,
    success: function (data) {
        $.each(data, function(key, value) {
            let name = this.name;
            let tid = key;
            let approved = this.approved;
            let pids = this.pids;
            $.each(pids, function(key, pid) {
                $('.tags-input[pid='+pid+']').tagsinput('add', {'tid': parseInt(tid), 'name': name, 'approved': approved}, {preventPost: true});
            });
        });
    },
    error: function(msg) {
        alert("Error fetching data from server");
    }
});

// add tag
$('.tags-input').on('beforeItemAdd', function(event) {  
    // dont post tags that are added automatically on page load
    if(event.options && event.options.preventPost) {
        return;
    }
    
    let pid = $(this).attr("pid");
    let tid = event.item.tid;
    let name = event.item.name;
    let input = $(this);
    
    let isMaster = $(this).tagsinput()[0].options.isMaster;
    let data = { tid: tid, pid: pid, tag: name, approved: isMaster };        
    
    // check for duplicate tags
    $.each(input.tagsinput('items'), function(key, value) {
        if(value.name == name) {
            input.closest(".twitter-typeahead .tt-open").removeClass("tt-open");
            event.cancel = true;
            return;
        }
    });
    
    $.ajax({
        method: "POST",
        url: "inc/ajax.php?action=addTag",
        data: data,
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
            if (msg.responseText.includes("Duplicate entry")) {
                console.log(msg.responseText);
            } else {
                alert("No response");
            }
        }
    });
});

$('.tags-input').on('beforeItemAddSuperMode', function(event) {
    let val = event.item;
                    
    if(val == "") {
        return;
    }
    
    let input = $('.tags-input');
    let approved = $(this).tagsinput()[0].options.isMaster;
    
    $.ajax({
        method: "POST",
        url: "inc/ajax.php?action=addTag",
        data: {approved: approved, tag: val},
        dataType: 'json',
        success: function (data) {
            if (data.success) {
                let item = {"tid": data.tid, "name": val, "approved": data.approved};
                input.parent().find(".tt-input").val("");
                console.log(item);
                event.self.add(item);
            } else {
                console.log("Error adding: " + data);
                alert("Error adding");
            }
        },
        error: function(msg) {
            if (msg.responseText.includes("Duplicate entry")) {
                console.log(msg.responseText);
            } else {
                alert("No response");
            }
        }
    });
});

// Scroll to top
$(window).scroll(function () {
    if ($(this).scrollTop() > 50) {
        $('.back-to-top').fadeIn();
    } else {
        $('.back-to-top').fadeOut();
    }
});

// scroll body to 0px on click
$('.back-to-top').click(function () {
    $('body,html').animate({
        scrollTop: 0
    }, 400);
    return false;
});

$('.dropdown-user .dropdown-item').click(function () {
    let role = $(this).attr("role");
    if(role == "logout") {
        $.get("inc/ajax.php?action=" + role, function(data) {
            if(data.success) {
                location.reload();
            }
        });
    }
});