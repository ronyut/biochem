var MAX_VISIBLE_TAGS = 30;

init();
let limitTags = false;

function init(){
    refreshCount();
}

function refreshCount() {
    let cnt = $(".article-container[show=true]").length;
    $("#cnt_visible").text(cnt);
}

$(document).on('click', '#showAllTags', function (e) {
    limitTags = !limitTags;
});

// Remove tag by ctrl+right click
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
                container.attr("show", "false"); // not found id + we want to enable filter  -> bye
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

function getActiveFilters() {
    let filters = $(".tag-active");
    let ids = [];
    filters.each(function(i, el){
        ids.push($(el).attr("tid"));
    });
    
    return ids;
}

function getInactiveFilters() {
    let filters = $(".tag-link:not(.tag-active)");
    let ids = [];
    filters.each(function(i, el){
        ids.push($(el).attr("tid"));
    });
    
    return ids;
}

// validate tags in the filter control
function validate(tid) {
    
    let inactive = getInactiveFilters();
    
    let tags = $(".article-container[show=true] .tags-input").tagsinput('items');
    let y = [];

    if ($(".article-container[show=true]").length > 1){
        // convert object of arrays of objects to: array of objects
        tags.forEach(arr => {
            for (let obj in arr){
                y.push(arr[obj]);
            }
        });
    } else {
        y = tags;
    }
    
    let i = 0;
    for (let filter in inactive){
        let fil = parseInt(inactive[filter]);

        if (typeof y === 'object'){
            if (!y.some(e => e.tid == fil)) {
                $(".tag-link[tid="+fil+"]").addClass("hidden");
            } else {
                $(".tag-link[tid="+fil+"]").removeClass("hidden");
            }
            
        }
        
        if(limitTags){
            if (i > MAX_VISIBLE_TAGS && $(".tag-link:not(.tag-active):not(.hidden)").length > MAX_VISIBLE_TAGS) {
                $(".tag-link[tid="+fil+"]").addClass("hidden");
            }
        }
        
        i++;
    }
}

function clearGoogleResults() {
    $(".google-results").html("");
    
    $(".highlight").contents().unwrap();
}

// search "google"
$("#googleForm").submit(function(e) {
    e.preventDefault();
    let query = $("#google").val();
    clearGoogleResults();

    if(query == "") {
        return;
    }
    
    $.ajax({
        method: "POST",
        url: "inc/find.php",
        data: { query: $("#google").val(), _s: Math.random() },
        dataType: 'json',
        cache: false,
        success: function (results) {
            let output = "<hr>";
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
                
                output += "<h"+i+"><a href='#' class='scroll-to' pid='"+id+"'>תוצאה "+i+"</a></h"+i+">";
                i++;
            }
            $(".google-results").html(output);
            $(".scroll-to:first").click(); // scroll to result
        },
        error: function(msg) {
            alert("No response");
        }
    });
});

$(document).on('click', '.scroll-to', function (e) {
    let id = $(this).attr("pid");
    $([document.documentElement, document.body]).animate({
        scrollTop: $(".article-container[pid="+id+"]").offset().top
    }, 250);
});

function escapeRegExp(string) {
  return string.replace(/[.*+\-?^${}()|[\]\\]/gi, '\\$&'); // $& means the whole matched string
}

function replaceAll(str, find, replace) {
  return str.replace(new RegExp(escapeRegExp(find), 'gi'), replace);
}

// enable tags
var tags = new Bloodhound({
  datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
  queryTokenizer: Bloodhound.tokenizers.whitespace,
  prefetch: {
    url: 'inc/tags.php',
    cache: false,
    filter: function(list) {
      return $.map(list, function(tagName) {
        return { name: tagName }; });
    }
  }
});
tags.initialize();

$('.tags-input').tagsinput({
    itemValue: 'tid',
    itemText: 'name',
    typeaheadjs: {
        name: 'tags',
        displayKey: 'name',
        valueKey: 'tid',
        source: tags.ttAdapter()
    }
});

$(".draggable").draggable();