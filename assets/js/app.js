var app = {
    initialize: function() {
        // Cache vars
        var doc = $(document);
        app._downloads = $('#downloads');
        app._subtitles = $('#subs_list');
        app._settingsForm = $('#form_settings');
        app._settings = {};
        app._putioCurrentFolder = null; // cache putio folder request

        // Settings
        var settings = app._settingsForm.serializeArray();

        $.each(settings, function(i, item){
            app._settings[item.name.replace('[', '_').replace(']', '')] = item.value;
        });

        // Get current downloads
        app.getDownloads();

        // Events
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


        doc.on('click', 'a.remove[data-type][data-id]', app.removeDownload);
        doc.on('click', '#subs_list a', app.downloadFile);
        doc.on('reset', 'form', function(){
            $('input[readonly]').prop('readonly', false);
        });
        doc.on('submit', '#form_freebox_adddownload', function(ev){
            ev.preventDefault();
            data = $(this).serializeArray();
            app.addDownload(data);
        });
        doc.on('submit', '#form_settings', app.saveSettings);
        doc.on('change', '#form_settings input', function(ev) {
            if (typeof app.changeSetting[this.id] !== 'undefined') {
                app.changeSetting[this.id](ev, this);
            }
        });
        doc.on('click', '.manual_sub_search button', function() {
            if (app._putioCurrentFolder === null) {
                return false;
            }
            app.getSubs(app._putioCurrentFolder.betaseries_id, app._putioCurrentFolder.files);
        });

        $('a.hasTooltip').tooltip();
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
                    $('.manual_sub_search button').prop('disabled', (data.totalResults === 0 || data.betaseries_id === null));
                    if (typeof data.files !== 'undefined' && typeof data.betaseries_id !== 'undefined') {
                        app._putioCurrentFolder = data;
                        if (app.checkSetting('settings_subtitles_autosearch', '1')) {
                            app.getSubs(data.betaseries_id, data.files);
                        }
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
        if (typeof show === 'undefined' || typeof files === 'undefined') {
            app._subtitles.html('<li class="nav-header">Aucun sous-titre</li>');
            return false;
        }
        var manual_sub_search = $('.manual_sub_search button:disabled');
        $.ajax({
            method: 'post',
            url: './api.php?bridge=subtitles',
            data: {
                'show': show,
                'files': files
            },
            dataType: 'JSON',
            cache: true,
            beforeSend: function() {
                manual_sub_search.prop('disabled', true);
            },
            success: function(data) {
                if (data.totalResults > 0) {
                    app._subtitles.html(data.resultHTML);
                }
                else {
                    app._subtitles.html('<li class="nav-header">Aucun sous-titre</li>');
                }
                manual_sub_search.prop('disabled', false);
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
    },
    saveSettings: function(ev) {
        ev.preventDefault();

        var f = $(this);
        var data = f.serializeArray();

        app._settings = {};
        $.each(data, function(i, item){
            app._settings[item.name.replace('[', '_').replace(']', '')] = item.value;
        });

        f.parents('.modal').modal('hide');

        $.ajax({
            method: this.method,
            url: this.action,
            data: data,
            dataType: 'JSON'
        });
    },
    checkSetting: function(name, val) {
        if (typeof app._settings[name] !== 'undefined' && app._settings[name] === val) {
            return true;
        }
        return false;
    },
    changeSetting: {
        settings_subtitles_autosearch: function(ev, that) {
            var manual_sub_search = $('.manual_sub_search');
            if (that.checked === true) {
                manual_sub_search.removeClass('on');
            }
            else {
                manual_sub_search.addClass('on');
            }
        }
    }
};

$(document).ready(app.initialize);