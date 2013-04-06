var app = {
    initialize: function() {
        var doc = $(document);

        $('form').on('submit', function(ev){
            ev.preventDefault();
            data = $(this).serializeArray();
            app.addDownload(data);
        });

        $('form').on('reset', function(ev) {
            $('input[readonly]').prop('readonly', false);
        });

        doc.on('click', 'a[data-type]', function(ev) {
            ev.preventDefault();
            var anchor = $(this);
            var type = anchor.data('type');
            if (type == 'file') {
                $('input[name=real_url]').val(this.href);
                $('input[name=url]').val(anchor.data('nice_url')).prop('readonly', true);
                $('input[name=file]').val(anchor.text());
                $(window).scrollTop($('form legend').scrollTop());
            }
            else if (type == 'folder') {
                app.getFiles(type, {'parent_id': anchor.data('folder_id')}, $('#files_list'));
            }
        });

        app._downloads = $('#downloads');
        app.getDownloads();

        app._subtitles = $('#subs_list');

        doc.on('click', 'a.remove[data-type][data-id]', app.removeDownload);
        doc.on('click', '#subs_list a', app.downloadFile);
    },
    getFiles: function(type, data, target) {
        $.ajax({
            method: 'post',
            url: './api.php?bridge=' + type,
            data: data,
            dataType: 'JSON',
            beforeSend: function() {
                app._subtitles.html('<li class="nav-header">Aucun sous-titre</li>');
            },
            success: function(data) {
                if (typeof data.resultHTML !== 'undefined') {
                    if (typeof data.files !== 'undefined' && typeof data.betaseries_id !== 'undefined') {
                        app.getSubs(data.betaseries_id, data.files);
                    }
                    target.html(data.resultHTML);
                }
            }
        });
    },
    addDownload: function(postData) {
        $.ajax({
            method: 'post',
            url: "./api.php?bridge=freebox",
            data: data,
            beforeSend: function() {
                $('form input').prop('disabled', 'disabled');
            },
            success: function(data) {
                $('form input').prop('disabled', false);
                $('#log').show().html('Téléchargement lancé');
                $('form')[0].reset();
                app.getDownloads();
            }
        });
    },
    getDownloads: function() {
        $.ajax({
            method: 'post',
            url: './api.php?bridge=freebox',
            data: {
                'downloads': true
            },
            dataType: 'JSON',
            success: function(data) {
                if (data.totalResults > 0) {
                    app._downloads.html(data.resultHTML);
                    if (data.running) {
                        setTimeout(app.getDownloads, 2000);
                        $('#log').html('').hide();
                    }
                }
                else {
                    app._downloads.html('<p class="muted">Aucun téléchargement en cours ou terminé.</p>');
                }
            }
        });
    },
    removeDownload: function() {
        var anchor = $(this);
        $.ajax({
            method: 'post',
            url: './api.php?bridge=freebox',
            data: {
                'removeDownload': true,
                type: anchor.data('type'),
                id: anchor.data('id')
            },
            dataType: 'JSON',
            success: function(data) {
                app.getDownloads();
            }
        });
    },
    getSubs: function(show, files) {
        $.ajax({
            method: 'post',
            url: './api.php?bridge=subtitles',
            data: {
                'show': show,
                'files': files
            },
            dataType: 'JSON',
            success: function(data) {
                if (data.totalResults > 0) {
                    app._subtitles.html(data.resultHTML);
                }
                else {
                    app._subtitles.html('<li class="nav-header">Aucun sous-titre</li>');
                }
            }
        });
    },
    downloadFile: function(ev) {
        /**

            TODO:
            - Terminer la partie téléchargement des sous-titres liés à un épisode

        **/

        /*
        ev.preventDefault();

        $.ajax({
            method: 'post',
            url: './api.php?bridge=download',
            data: {
                'url': this.href,
                'filename': this.innerText,
                'show_title': $(this).data('show_title'),
                'show_season': $(this).data('show_season'),
                'show_episode': $(this).data('show_episode')
            },
            dataType: 'JSON',
            success: function(data) {
                console.log(data);
            }
        });
        */
    }
};

$(document).ready(app.initialize);