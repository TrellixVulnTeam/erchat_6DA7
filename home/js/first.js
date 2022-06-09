$(document).ready(function(){
    // Search Button:
    $('.button-holder').on('click', function() {
        document.search_form.submit();
    });

    // Button for profile post:
    $('#submit_profile_post').click(function(){
        $.ajax({
            type: "POST",
            url: "ajax_submit_profile_post.php",
            data: $('form.profile_post').serialize(),
            success: function(msg) {
                $("$post_form").modal('hide');
                location.reload();
            },
            error: function() {
                alert('Failure');
            }
        });
    });  
});

$(document).click(function(e){

	if(e.target.class != "search_results" || e.target.id != "nav-search--input") {

		$(".search_results").html("");
		$('.search_results_footer').html("");
		$('.search_results_footer').toggleClass("search_results_footer_empty");
		$('.search_results_footer').toggleClass("search_results_footer");
	}

	if(e.target.className != "dropdown_data_window") {

		$(".dropdown_data_window").html("");
		$(".dropdown_data_window").css({"padding" : "0px", "height" : "0px"});
	}


});

function getUser(value, user) {
    $.post("ajax_friend_search.php", {query:value, userLoggedIn:user}, function(data) {
        $(".results").html(data);
    });
}

function getDropdownData(user, type) {
    if($(".dropdown_data_window").css("height") == "0px") {
        var pageName;
        if(type == 'notification') {
            pageName = "ajax_load_notifications.php";
            $("span").remove("#unread_notification");
        
        }   else if(type == 'message') {
                pageName = "ajax_load_messages.php";
                $("span").remove("#unread_message");
        }

        var ajaxreq = $.ajax({
            url: "/erchat/home/" + pageName,
            type: "POST",
            data: "page=1&userLoggedIn=" + user,
            cache: false,

            success: function(response) {
                $(".dropdown_data_window").html(response);
                $(".dropdown_data_window").css({"padding": "4px 1rem 0 0", "height": "28rem"});
                $("#dropdown_data_type").val(type);
            }
        });
    }   else    {
        $(".dropdown_data_window").html("");
        $(".dropdown_data_window").css({"padding": "0", "height": "0px"});
    }
}

function getLiveSearchUsers(value, user) {
    $.post("ajax_search.php", {query:value, userLoggedIn:user}, function(data) {
        if($(".search_results_footer_empty")[0]) {
			$(".search_results_footer_empty").toggleClass("search_results_footer");
			$(".search_results_footer_empty").toggleClass("search_results_footer_empty");
		}

		$('.search_results').html(data);
		// $('.search_results').css({"padding" : "0px", "height" : "0px"});
		$('.search_results_footer').html("<a class='mvResult' href='search.php?q=" + value + "'>مشاهده همه</a>");

		if(data == "") {
			$('.search_results_footer').html("");
			$('.search_results_footer').toggleClass("search_results_footer_empty");
			$('.search_results_footer').toggleClass("search_results_footer");
		}
    });
}

// Back to Top code:
var btn = $('#backToTopBtn');

$(window).scroll(function() {
  if ($(window).scrollTop() > 300) {
    btn.addClass('show');
  } else {
    btn.removeClass('show');
  }
});

btn.on('click', function(e) {
  e.preventDefault();
  $('html, body').animate({scrollTop:0}, '300');
});