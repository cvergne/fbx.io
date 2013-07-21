var app = {
    initialize: function() {
        // Cache vars
        var doc = $(document);
        app._downloads = $('#downloads');
        app._subtitles = $('#subs_list');
        app._settingsForm = $('#form_settings');
        app._settings = {};
        app._settingsQueue = [];
        app._putioCurrentFolder = null; // cache putio folder request
        app._addDownload_realurl = $('input[name=real_url]');
        app._addDownload_url = $('input[name=url]');
        app._addDownload_file = $('input[name=file]');

        // Text
        app._lang = {
            downloads: {
                no_downloads: '<p class="text-muted text-center"><small>Aucun téléchargement en cours ou terminé.</small></p>'
            }
        };

        // Settings
        var settings = app._settingsForm.serializeArray();

        $.each(settings, function(i, item){
            app._settings[item.name.replace('[', '_').replace(']', '')] = item.value;
        });

        // Get current downloads
        app.getDownloads();

        // Get files in Download folder
        app.getFbxFiles();

        // Events
        doc.on('click', '#target-freebox-fs a[data-path]', app.removeFbxFile);
        app._addDownload_url.on('change', function(ev) {
            app._addDownload_file.val('');
        });

        doc.on('click', 'a[data-type]', function(ev) {
            ev.preventDefault();
            var anchor = $(this);
            var type = anchor.data('type');
            var nice_filename = '';
            if (type == 'file') {
                if (typeof anchor.data('nice_filename') !== 'undefined') {
                    nice_filename = anchor.data('nice_filename');
                }
                else {
                    nice_filename = anchor.find('span').text();
                }

                app.setDownload({
                    real_url: this.href,
                    url: anchor.data('nice_url'),
                    nice_filename: nice_filename
                });
            }
            else if (type == 'folder') {
                app.getFiles(type, {'parent_id': anchor.data('folder_id')}, $('#files_list'));
            }
        });

        doc.on('click', '#subs_folder a[data-nice_filename]', function(ev){
            ev.preventDefault();

            var anchor = $(this);
            var nice_filename = anchor.data('nice_filename');
            var origin_filename = anchor.data('origin_filename');
            var uri = this.href;

            app.setDownload({
                real_url: uri,
                url: origin_filename,
                nice_filename: nice_filename
            });
        });
        doc.on('click', '#subs_folder .backfromfolder', function(ev){
            ev.preventDefault();
            $(this).parents('.infolder').removeClass('infolder');
            $('#subs_folder').html('');
        });

        doc.on('click', 'a.api_guess_filename', app.guessFilename);

        doc.on('click', '#remove_all_downloads', app.removeAllDownloads);
        doc.on('click', 'a.remove[data-type][data-id]', app.removeDownload);
        doc.on('click', '#subs_list a', app.downloadSub);
        doc.on('reset', 'form', function(){
            $('input[readonly]').prop('readonly', false);
            $('.guess').removeClass('inactive');
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
            app.getSubs(app._putioCurrentFolder.betaseries_id, app._putioCurrentFolder.files, app._putioCurrentFolder.cleanfiles);
        });

        $('a.hasTooltip').tooltip();
    },
    guessFilename: function(ev) {
        ev.preventDefault();
        var anchor = $(this);
        /* original_filename */
        if (app._addDownload_file.val() !== '') {
            original_filename = app._addDownload_file.val();
        }
        else {
            original_filename = app._addDownload_url.val();
        }

        if (original_filename !== '') {
            app._addDownload_file.attr('disabled', 'disabled');
            app._addDownload_file.attr('disabled', 'disabled');

            $.ajax({
                method: 'post',
                url: './api.php?bridge=self_guessfilename',
                data: {
                    'ajax': true,
                    'uri': original_filename
                },
                dataType: 'json',
                success: function(data) {
                    app._addDownload_file.attr('disabled', false);
                    if (typeof data.root !== 'undefined' && !data.root.error) {
                        app._addDownload_file.val(data.root.filename);
                    }
                }
            });
        }
    },
    getFbxFiles: function() {
        $('#target-freebox-fs').load('./api.php?bridge=freebox_files');
    },
    removeFbxFile: function() {
        if (confirm('Supprimer DÉFINITIVEMENT ce fichier ? (action irréversible)')) {
            var anchor = $(this);
            $.ajax({
                method: 'post',
                url: './api.php?bridge=freebox_files',
                data: {
                    'ajax': true,
                    'rm': true,
                    'path': anchor.data('path')
                },
                dataType: 'html',
                success: function(data) {
                    $('#target-freebox-fs').html(data);
                }
            });
        }
    },
    getFiles: function(type, data, target) {
        $.ajax({
            method: 'post',
            url: './api.php?bridge=' + type,
            data: $.extend(data, {'ajax': true}),
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
                            app.getSubs(data.betaseries_id, data.files, data.cleanfiles);
                        }
                    }
                    target.html(data.resultHTML);
                }
            }
        });
    },
    refreshFiles: function() {
        var currentFolderID = 0;
        if (app._putioCurrentFolder !== null) {
            currentFolderID = app._putioCurrentFolder.currentFolderID;
        }
        app.getFiles('folder', {'parent_id': currentFolderID}, $('#files_list'));
    },
    setDownload: function(dlDatas) {
        app._addDownload_realurl.val(dlDatas.real_url);
        app._addDownload_url.val(dlDatas.url).prop('readonly', true);
        if (dlDatas.nice_filename !== '') {
            app._addDownload_file.val(dlDatas.nice_filename);
        }
        else {
            app._addDownload_file.val(dlDatas.url);
        }

        $(window).scrollTop($('form legend').scrollTop());
        $('#addDownloadForm').collapse('show');
        $('.guess').addClass('inactive');
    },
    addDownload: function(postData) {
        $.ajax({
            method: 'post',
            url: "./api.php?bridge=freebox",
            data: $.extend(data, {'ajax': true}),
            beforeSend: function() {
                $('form input').prop('disabled', 'disabled');
            },
            success: function(data) {
                $('form input').prop('disabled', false);
                $('#log').show().html('Téléchargement lancé');
                $('form')[0].reset();
                app.getDownloads();
                $('#addDownloadForm').collapse('hide');
            }
        });
    },
    getDownloads: function() {
        $.ajax({
            method: 'post',
            url: './api.php?bridge=freebox',
            data: {
                'ajax': true,
                'downloads': true
            },
            dataType: 'JSON',
            success: function(data) {
                if (data.finishedCount > 0) {
                    $('#remove_all_downloads').addClass('active');
                }
                else {
                    $('#remove_all_downloads').removeClass('active');
                }
                if (data.totalResults > 0) {
                    app._downloads.html(data.resultHTML);
                    if (data.running) {
                        setTimeout(app.getDownloads, 2000);
                        $('#log').html('').hide();
                    }
                }
                else {
                    app._downloads.html(app._lang.downloads.no_downloads);
                }
            }
        });
    },
    removeAllDownloads: function(ev) {
        ev.preventDefault();
        $.each($('#downloads a.finished[data-id]'), app.removeDownload);
        $(this).removeClass('active');
    },
    removeDownload: function() {
        var anchor = $(this);
        $.ajax({
            method: 'post',
            url: './api.php?bridge=freebox',
            data: {
                'ajax': true,
                'removeDownload': true,
                type: anchor.data('type'),
                id: anchor.data('id')
            },
            dataType: 'JSON',
            success: function(data) {
                var dl = anchor.parents('.dl');
                dl.next('hr').remove();
                dl.remove();
                if ($('.dl').length === 0) {
                    app._downloads.html(app._lang.downloads.no_downloads);
                }
                if ($('.dl .label-success').length === 0) {
                    $('#remove_all_downloads').removeClass('active');
                }
            }
        });
    },
    getSubs: function(show, files, cleanfiles) {
        if (typeof show === 'undefined' || typeof files === 'undefined') {
            app._subtitles.html('<li class="nav-header">Aucun sous-titre</li>');
            return false;
        }
        var manual_sub_search = $('.manual_sub_search button:disabled');
        $.ajax({
            method: 'post',
            url: './api.php?bridge=subtitles',
            data: {
                'ajax': true,
                'show': show,
                'files': files,
                'cleanfiles': cleanfiles
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
    downloadSub: function(ev) {
        /**

            TODO:
            - Terminer la partie téléchargement des sous-titres liés à un épisode

        **/

        ev.preventDefault();

        $.ajax({
            method: 'post',
            url: './api.php?bridge=download',
            data: {
                'ajax': true,
                'url': this.href,
                'filename': this.innerText,
                'nicename': $(this).data('nice_filename')
            },
            dataType: 'JSON',
            success: function(data) {
                if (typeof data.root !== 'undefined' && typeof data.error !== 'undefined' && !data.error) {
                    if (typeof data.root.html !== 'undefined') {
                        $('#subs_folder').parent('.well').addClass('infolder');
                        $('#subs_folder').html(data.root.html);
                    }
                    else if(typeof data.root.completepath !== 'undefined') {
                        app.setDownload({
                            real_url: data.root.completepath,
                            url: data.root.originfilename,
                            nice_filename: data.root.newfilename
                        });
                    }
                }
            }
        });
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
            data: $.extend(data, {'ajax': true}),
            dataType: 'JSON',
            success: function() {
                if (app._settingsQueue.length > 0) {
                    $.each(app._settingsQueue, function(i, fx){
                        fx();
                    });
                }
                app._settingsQueue = [];
            }
        });
    },
    checkSetting: function(name, val) {
        if (typeof app._settings[name] !== 'undefined' && app._settings[name] === val) {
            return true;
        }
        return false;
    },
    changeSetting: {
        settings_filename_autoputio: function(ev, that) {
            if (that.checked !== true && app._addDownload_realurl.val() !== '' && app.checkSetting('settings_filename_guessoption', '1')) {
                $('.guess').removeClass('inactive');
            }
            app._settingsQueue.push(app.refreshFiles);
        },
        settings_filename_guessoption: function(ev, that) {
            var guess = $('.guess');
            if (that.checked === true) {
                guess.removeClass('hidden');
            }
            else {
                guess.addClass('hidden');
            }
        },
        settings_putio_hidespace: function(ev, that) {
            var putio_space = $('.putio_space');
            if (that.checked === true) {
                putio_space.addClass('off');
            }
            else {
                putio_space.removeClass('off').load('./api.php?bridge=putio_storage');
            }
        },
        settings_subtitles_autosearch: function(ev, that) {
            var manual_sub_search = $('.manual_sub_search');
            if (that.checked === true) {
                manual_sub_search.removeClass('off');
            }
            else {
                manual_sub_search.addClass('off');
            }
        }
    }
};

$(document).ready(app.initialize);