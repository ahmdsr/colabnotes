<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collaborative Notes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/github-markdown-css/5.1.0/github-markdown.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f5f7;
            color: #333;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #2c3e50;
            font-size: 1.75rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        h2 {
            color: #34495e;
            font-size: 1.25rem;
            font-weight: 600;
        }
        .btn-primary {
            background-color: #4a5568;
            border-color: #4a5568;
        }
        .btn-primary:hover {
            background-color: #2d3748;
            border-color: #2d3748;
        }
        .form-control {
            border-color: #e2e8f0;
        }
        .form-control:focus {
            border-color: #4a5568;
            box-shadow: 0 0 0 0.2rem rgba(74, 85, 104, 0.25);
        }
        .editor-toolbar {
            border-color: #e2e8f0;
        }
        .editor-toolbar button {
            color: #4a5568 !important;
        }
        .editor-toolbar button:hover,
        .editor-toolbar button.active {
            background-color: #edf2f7 !important;
        }
        .CodeMirror {
            border-color: #e2e8f0;
        }
        .editor-statusbar {
            color: #718096;
        }
        .note-section {
            background-color: #f8fafc;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e2e8f0;
        }
        .text-muted {
            color: #718096 !important;
        }
        .markdown-body {
            background-color: transparent;
            min-height: 200px;
            color:#6c757d
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>Don't hesitate, Collaborate!</h1>
        
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="note-section">
                    <h2 class="mb-3">EMBA Notes</h2>
                    <div id="emba-display" class="markdown-body" style="min-height: 200px; color:#6c757d"></div>
                    <textarea id="emba-notes" class="form-control d-none"></textarea>
                    <button id="edit-emba" class="btn btn-secondary mt-3">Edit EMBA Notes</button>
                    <button id="save-emba" class="btn btn-primary mt-3 d-none">Save EMBA Notes</button>
                    <p id="emba-last-edited" class="mt-2 text-muted small"></p>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="note-section">
                    <h2 class="mb-3">International Business Notes</h2>
                    <div id="ib-display" class="markdown-body" style=""></div>
                    <textarea id="ib-notes" class="form-control d-none"></textarea>
                    <button id="edit-ib" class="btn btn-secondary mt-3">Edit IB Notes</button>
                    <button id="save-ib" class="btn btn-primary mt-3 d-none">Save IB Notes</button>
                    <p id="ib-last-edited" class="mt-2 text-muted small"></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/marked/4.0.2/marked.min.js"></script>
    <script>
        $(document).ready(function() {
            const mdeOptions = {
                spellChecker: false,
                autosave: {
                    enabled: false
                },
                status: false
            };
            let embaMde, ibMde;

            function initializeEditor(type) {
                const element = document.getElementById(`${type}-notes`);
                return new EasyMDE({ ...mdeOptions, element });
            }

            function loadNotes() {
                $.get('get_notes.php', function(data) {
                    $('#emba-display').html(marked.parse(data.emba.content));
                    $('#ib-display').html(marked.parse(data.ib.content));
                    $('#emba-notes').val(data.emba.content);
                    $('#ib-notes').val(data.ib.content);
                    $('#emba-last-edited').text('Last edited by ' + data.emba.username + ' on ' + data.emba.date);
                    $('#ib-last-edited').text('Last edited by ' + data.ib.username + ' on ' + data.ib.date);
                });
            }

            function saveNotes(type) {
                const content = type === 'emba' ? embaMde.value() : ibMde.value();
                const username = prompt('Please enter your username:');
                if (username) {
                    $.post('save_notes.php', { type: type, content: content, username: username }, function(data) {
                        if (data.success) {
                            loadNotes();
                            alert('Notes saved successfully!');
                            toggleEditMode(type, false);
                        } else {
                            alert('Error saving notes. Please try again.');
                        }
                    });
                }
            }

            function toggleEditMode(type, edit) {
                $(`#${type}-display`).toggleClass('d-none', edit);
                $(`#${type}-notes`).toggleClass('d-none', !edit);
                $(`#edit-${type}`).toggleClass('d-none', edit);
                $(`#save-${type}`).toggleClass('d-none', !edit);

                if (edit) {
                    if (type === 'emba') {
                        embaMde = initializeEditor('emba');
                    } else {
                        ibMde = initializeEditor('ib');
                    }
                } else {
                    if (type === 'emba') {
                        embaMde.toTextArea();
                        embaMde = null;
                    } else {
                        ibMde.toTextArea();
                        ibMde = null;
                    }
                }
            }

            $('#edit-emba').click(() => toggleEditMode('emba', true));
            $('#edit-ib').click(() => toggleEditMode('ib', true));

            $('#save-emba').click(() => saveNotes('emba'));
            $('#save-ib').click(() => saveNotes('ib'));

            loadNotes();
        });
    </script>
</body>
</html>


