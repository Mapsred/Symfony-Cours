/**
 * Created by Maps_red on 05/05/2016.
 */

var Voting = {
    init: function () {
        var types = ['No', 'Yes'];
        $.each(types, function (key, type) {
            $("[id^=vote" + type + "]").click(function () {
                var quote = $(this).data("quote");
                type = type.toLowerCase();
                Voting.ajaxVote(type, quote);
                Voting.ajaxLoadVotes();
            });
        });
    },

    ajaxVote: function (type, quote) {
        $.ajax({
            type: "POST",
            url: "/ajax/vote",
            data: {type: type, quote: quote, vote: true},
            success: function (res) {
                var returnMessage = $("#returnMessage"+res['id']);
                Voting.cleanMessage(returnMessage);
                returnMessage.addClass("text-"+res['type']);
                returnMessage.html(res['message']);

                Voting.ajaxLoadVotes();
            }
        });
    },

    ajaxLoadVotes: function () {
        $.ajax({
            type: "POST",
            url: "/ajax/count",
            data: {ids: Voting._grepQuoteIds},
            success: function (res) {
                Voting._loadVotesCount(res);
            }
        });
    },

    _grepQuoteIds: function () {
        var Numbers = [];
        var nb = 0;
        $.each($("[id^=yes]"), function () {
            Numbers[nb] = $(this).data("quote");
            nb++;
        });

        return Numbers;
    },

    _loadVotesCount: function (res) {
        var types = ['no', 'yes'];
        $.each(types, function (key, type) {
            $.each($("[id^=" + type + "]"), function () {
                var id = $(this).data("quote");
                $(this).html(res[id][type]);
            });
        });
    },

    cleanMessage : function (obj) {
        obj.removeClass("danger");
        obj.removeClass("success");
    }
};


$(document).ready(function () {
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });
    Voting.init();
    Voting.ajaxLoadVotes();
});
