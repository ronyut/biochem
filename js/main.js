var MAX_VISIBLE_TAGS = 30;

init();

function init(){
    refreshCount();
}

function refreshCount() {
    let cnt = $(".article-container[show=true]").length;
    $("#cnt_visible").text(cnt);
}

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
        
        if (i > MAX_VISIBLE_TAGS && $(".tag-link:not(.tag-active):not(.hidden)").length > MAX_VISIBLE_TAGS) {
            $(".tag-link[tid="+fil+"]").addClass("hidden");
        }
        
        i++;
    }
    
    
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