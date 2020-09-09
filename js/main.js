// active and inactive questions down in page
let activeDown = [];
let inactiveDown = [];
// active and inactive questions down in page
let activeUp = [];
let inactiveUp = [];

init();

function init(){
    $(".tags-active .tag-link.really").each(function(i, element){
        let tid = $(element).attr("tid");
        activeUp.push(tid);
        activeDown.push(tid);
    });
}

// Remove tag by ctrl+right click
$(document).on('click', '.tag-link', function (e) {
    $(this).removeClass("really").addClass("not-really");
    let name = $(this).text();
    let tid = $(this).attr("tid");
    let isActive = $(this).hasClass("tag-active");
    
    // if going to be inactive
    if(isActive) {
        $(".tags-inactive .tag-link[tid="+tid+"]").removeClass("not-really").addClass("really");
        inactiveUp.push(tid);
        activeUp.splice(activeUp.indexOf(tid), 1);
    } else {
        $(".tags-active .tag-link[tid="+tid+"]").removeClass("not-really").addClass("really");
        activeUp.push(tid);
        inactiveUp.splice(activeUp.indexOf(tid), 1);
    }
    
    $(".tags-input").each(function(i, el){
        let tags = $(el).tagsinput('items');
        if (tags.some(e => e.tid == tid)) {
            let container = $(el).closest(".article-container");
            if (isActive) {
                container.attr("show", "false");
                
                for(let tag in tags) {
                    inactiveDown.push(tags[tag].tid);
                }
                
                activeDown.splice(activeDown.indexOf(tid), 1);
            } else {
                container.attr("show", "true");
                for(let tag in tags) {
                    activeDown.push(tags[tag].tid);
                }
                inactiveDown.splice(inactiveDown.indexOf(tid), 1);
            }
        }
    });
    
    validate(tid, activeDown, inactiveDown, activeUp, inactiveUp);
});

// validate tags in the filter control
function validate(tid, activeDown, inactiveDown, activeUp, inactiveUp) {
    
    /*console.log(activeUp);
    console.log(inactiveUp);
    console.log(inactiveDown);
    console.log(inactiveDown);*/
    
    // Active
    for(let tid in activeUp) {
        if (!activeDown.includes(tid)) {
            $(".tags-active .tag-link[tid="+tid+"]").removeClass("really").addClass("not-really");
            $(".tags-inactive .tag-link[tid="+tid+"]").removeClass("not-really").addClass("really");
        }
    }
    
    // Inactive
    for(let tid in inactiveUp) {
        if (!inactiveDown.includes(tid)) {
            $(".tags-inactive .tag-link[tid="+tid+"]").removeClass("really").addClass("not-really");
            $(".tags-active .tag-link[tid="+tid+"]").removeClass("not-really").addClass("really");
        }
    }
    
    console.log("done!");
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