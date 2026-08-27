<template>
    <div class="row">

        <!-- =====================================================
             LEFT SIDE
        ====================================================== -->

        <div class="col-lg-8">

            <!-- =================================================
                 EDITOR CARD
            ================================================== -->

            <div class="card shadow-sm mb-3">

                <div
                    class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2"
                >

                    <h4 class="mb-0">
                        Edit Title
                    </h4>

                    <div>

                        <button
                            type="button"
                            class="btn btn-outline-primary btn-sm me-1"
                            @click="openFindReplace"
                        >
                            🔎 Find & Replace
                        </button>

                        <button
                            type="button"
                            class="btn btn-outline-secondary btn-sm me-1"
                            @click="showPreview = true"
                        >
                            Preview
                        </button>

                        <a
                            href="/"
                            class="btn btn-secondary btn-sm"
                        >
                            Back
                        </a>

                    </div>

                </div>

                <div class="card-body">

                    <form
                        ref="editForm"
                        :action="`/update/${item.id}`"
                        method="POST"
                        @submit.prevent="submitForm"
                    >

                        <!-- =================================================
                             CSRF
                        ================================================== -->

                        <input
                            type="hidden"
                            name="_token"
                            :value="csrf"
                        >

                        <!-- =================================================
                             TITLE
                        ================================================== -->

                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Title
                            </label>

                            <input
                                type="text"
                                name="title"
                                v-model="title"
                                class="form-control"
                                placeholder="Enter title"
                                required
                            >

                            <div class="form-text">
                                Title:
                                {{ titleWordCount }}
                                words |
                                {{ titleCharCount }}
                                characters
                            </div>

                        </div>

                        <!-- =================================================
                             STATUS
                        ================================================== -->

                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Status
                            </label>

                            <select
                                name="status"
                                class="form-select"
                                v-model="status"
                            >

                                <option value="draft">
                                    Draft
                                </option>

                                <option value="published">
                                    Published
                                </option>

                                <option value="archived">
                                    Archived
                                </option>

                            </select>

                        </div>

                        <!-- =================================================
                             DESCRIPTION
                        ================================================== -->

                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Description
                            </label>

                            <textarea
                                id="editor"
                            ></textarea>

                            <input
                                type="hidden"
                                name="description"
                                :value="description"
                            >

                            <!-- =================================================
                                 AUTOSAVE STATUS
                            ================================================== -->

                            <div
                                class="autosave-status mt-2"
                            >

                                <!-- UNSAVED -->
                                <span
                                    v-if="saveStatus === 'unsaved'"
                                    class="status-item status-unsaved"
                                >
                                    <span class="status-dot"></span>
                                    🟡 Unsaved changes
                                </span>

                                <!-- SAVING -->
                                <span
                                    v-else-if="saveStatus === 'saving'"
                                    class="status-item status-saving"
                                >
                                    <span class="spinner-border spinner-border-sm"></span>
                                    🔵 Saving...
                                </span>

                                <!-- SAVED -->
                                <span
                                    v-else-if="saveStatus === 'saved'"
                                    class="status-item status-saved"
                                >
                                    <span class="status-dot"></span>
                                    🟢 All changes saved
                                </span>

                                <!-- ERROR -->
                                <span
                                    v-else-if="saveStatus === 'error'"
                                    class="status-item status-error"
                                >
                                    <span class="status-dot"></span>
                                    🔴 Save failed
                                </span>

                                <span
                                    v-if="saveStatus === 'saved' && lastSaved"
                                    class="text-muted small ms-2"
                                >
                                    {{ lastSaved }}
                                </span>

                            </div>

                        </div>

                        <!-- =================================================
                             CONTENT STATISTICS
                        ================================================== -->

                        <div class="card border-0 shadow-sm mb-3">

                            <div class="card-header bg-light">

                                <div
                                    class="d-flex justify-content-between align-items-center"
                                >

                                    <strong>
                                        📊 Content Statistics
                                    </strong>

                                    <span class="badge bg-primary">
                                        {{ statistics.readingTime }}
                                    </span>

                                </div>

                            </div>

                            <div class="card-body">

                                <div class="row g-3">

                                    <!-- WORDS -->

                                    <div class="col-6 col-md-4 col-lg-2">

                                        <div class="stat-box">

                                            <div class="stat-icon">
                                                📝
                                            </div>

                                            <div class="stat-value">
                                                {{ statistics.words }}
                                            </div>

                                            <div class="stat-label">
                                                Words
                                            </div>

                                        </div>

                                    </div>

                                    <!-- CHARACTERS -->

                                    <div class="col-6 col-md-4 col-lg-2">

                                        <div class="stat-box">

                                            <div class="stat-icon">
                                                🔤
                                            </div>

                                            <div class="stat-value">
                                                {{ statistics.characters }}
                                            </div>

                                            <div class="stat-label">
                                                Characters
                                            </div>

                                        </div>

                                    </div>

                                    <!-- PARAGRAPHS -->

                                    <div class="col-6 col-md-4 col-lg-2">

                                        <div class="stat-box">

                                            <div class="stat-icon">
                                                📄
                                            </div>

                                            <div class="stat-value">
                                                {{ statistics.paragraphs }}
                                            </div>

                                            <div class="stat-label">
                                                Paragraphs
                                            </div>

                                        </div>

                                    </div>

                                    <!-- HEADINGS -->

                                    <div class="col-6 col-md-4 col-lg-2">

                                        <div class="stat-box">

                                            <div class="stat-icon">
                                                🏷️
                                            </div>

                                            <div class="stat-value">
                                                {{ statistics.headings }}
                                            </div>

                                            <div class="stat-label">
                                                Headings
                                            </div>

                                        </div>

                                    </div>

                                    <!-- LINKS -->

                                    <div class="col-6 col-md-4 col-lg-2">

                                        <div class="stat-box">

                                            <div class="stat-icon">
                                                🔗
                                            </div>

                                            <div class="stat-value">
                                                {{ statistics.links }}
                                            </div>

                                            <div class="stat-label">
                                                Links
                                            </div>

                                        </div>

                                    </div>

                                    <!-- IMAGES -->

                                    <div class="col-6 col-md-4 col-lg-2">

                                        <div class="stat-box">

                                            <div class="stat-icon">
                                                🖼️
                                            </div>

                                            <div class="stat-value">
                                                {{ statistics.images }}
                                            </div>

                                            <div class="stat-label">
                                                Images
                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <!-- =================================================
                             FORM BUTTONS
                        ================================================== -->

                        <div class="d-flex gap-2 flex-wrap">

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                Update
                            </button>

                            <button
                                type="button"
                                class="btn btn-outline-primary"
                                @click="openFindReplace"
                            >
                                🔎 Find & Replace
                            </button>

                            <button
                                type="button"
                                class="btn btn-outline-secondary"
                                @click="showPreview = true"
                            >
                                Preview
                            </button>

                        </div>

                    </form>

                </div>

            </div>

            <!-- =====================================================
                 COMMENTS
            ====================================================== -->

            <div class="card shadow-sm">

                <div class="card-header">

                    <h5 class="mb-0">
                        Comments / Notes
                    </h5>

                </div>

                <div class="card-body">

                    <div
                        v-if="comments.length === 0"
                        class="text-muted"
                    >
                        No comments yet.
                    </div>

                    <div
                        v-for="comment in comments"
                        :key="comment.id"
                        class="border rounded p-2 mb-2"
                    >

                        <div
                            class="d-flex justify-content-between"
                        >

                            <strong>
                                {{ comment.user_name || 'Anonymous' }}
                            </strong>

                            <small class="text-muted">
                                {{ formatDate(comment.created_at) }}
                            </small>

                        </div>

                        <p class="mb-0 mt-1">
                            {{ comment.body }}
                        </p>

                    </div>

                </div>

                <div class="card-footer">

                    <form @submit.prevent="addComment">

                        <div class="input-group">

                            <input
                                type="text"
                                class="form-control"
                                placeholder="Add a comment..."
                                v-model="newComment"
                                required
                            >

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                Add
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

        <!-- =====================================================
             RIGHT SIDE
        ====================================================== -->

        <div class="col-lg-4">

            <div class="card shadow-sm">

                <div class="card-header">

                    <h5 class="mb-0">
                        Revisions History
                    </h5>

                </div>

                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-sm mb-0">

                            <thead class="table-light">

                                <tr>

                                    <th>
                                        Date
                                    </th>

                                    <th>
                                        Changed By
                                    </th>

                                    <th>
                                        Action
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                <tr
                                    v-for="rev in revisions"
                                    :key="rev.id"
                                >

                                    <td>
                                        {{ formatDate(rev.created_at) }}
                                    </td>

                                    <td>
                                        {{ rev.changed_by }}
                                    </td>

                                    <td>

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            @click="restoreRevision(rev)"
                                        >
                                            Restore
                                        </button>

                                    </td>

                                </tr>

                                <tr
                                    v-if="revisions.length === 0"
                                >

                                    <td
                                        colspan="3"
                                        class="text-center text-muted"
                                    >
                                        No revisions yet
                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- =========================================================
         FIND & REPLACE MODAL
    ========================================================== -->

    <div
        v-if="showFindReplace"
        class="modal fade show d-block"
        tabindex="-1"
        style="background: rgba(0, 0, 0, 0.5);"
    >

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">
                        🔎 Find & Replace
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        @click="closeFindReplace"
                    ></button>

                </div>

                <div class="modal-body">

                    <!-- FIND -->

                    <div class="mb-3">

                        <label class="form-label fw-semibold">
                            Find
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            v-model="findText"
                            placeholder="Enter text to find"
                            @keyup.enter="replaceFirst"
                        >

                    </div>

                    <!-- REPLACE -->

                    <div class="mb-3">

                        <label class="form-label fw-semibold">
                            Replace with
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            v-model="replaceText"
                            placeholder="Enter replacement text"
                        >

                    </div>

                    <!-- MESSAGE -->

                    <div
                        v-if="findMessage"
                        class="alert"
                        :class="
                            findMessageType === 'success'
                                ? 'alert-success'
                                : 'alert-warning'
                        "
                    >
                        {{ findMessage }}
                    </div>

                    <div class="small text-muted">
                        Find and replace text directly inside the
                        TinyMCE editor.
                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        @click="closeFindReplace"
                    >
                        Cancel
                    </button>

                    <button
                        type="button"
                        class="btn btn-warning"
                        @click="replaceFirst"
                    >
                        Replace First
                    </button>

                    <button
                        type="button"
                        class="btn btn-primary"
                        @click="replaceAll"
                    >
                        Replace All
                    </button>

                </div>

            </div>

        </div>

    </div>


    <!-- =========================================================
         PREVIEW MODAL
    ========================================================== -->

    <div
        v-if="showPreview"
        class="modal fade show d-block"
        tabindex="-1"
        style="background: rgba(0, 0, 0, 0.5);"
    >

        <div
            class="modal-dialog modal-lg modal-dialog-scrollable"
        >

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Preview
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        @click="showPreview = false"
                    ></button>

                </div>

                <div class="modal-body">

                    <h4>
                        {{ title }}
                    </h4>

                    <span
                        class="badge mb-3"
                        :class="{
                            'bg-success':
                                status === 'published',

                            'bg-warning text-dark':
                                status === 'draft',

                            'bg-secondary':
                                status === 'archived'
                        }"
                    >
                        {{ status }}
                    </span>

                    <div
                        v-html="description"
                        class="border rounded p-3 bg-white"
                    ></div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        @click="showPreview = false"
                    >
                        Close
                    </button>

                </div>

            </div>

        </div>

    </div>

</template>


<script>

export default {

    data() {

        const el =
            document.getElementById('app')

        const item =
            JSON.parse(
                el.dataset.item
            )

        return {

            item,

            /* =================================================
               FORM DATA
            ================================================= */

            title:
                item.title || '',

            description:
                item.description || '',

            status:
                item.status || 'draft',

            csrf:
                document
                    .querySelector(
                        'meta[name="csrf-token"]'
                    )
                    .getAttribute('content'),

            /* =================================================
               DATA
            ================================================= */

            revisions:
                JSON.parse(
                    el.dataset.revisions || '[]'
                ),

            comments:
                JSON.parse(
                    el.dataset.comments || '[]'
                ),

            newComment: '',

            /* =================================================
               AUTOSAVE
            ================================================= */

            saveStatus: 'saved',

            lastSaved: '',

            autosaveTimer: null,

            autosaveRequest: null,

            /* =================================================
               PREVIEW
            ================================================= */

            showPreview: false,

            /* =================================================
               FIND & REPLACE
            ================================================= */

            showFindReplace: false,

            findText: '',

            replaceText: '',

            findMessage: '',

            findMessageType: 'success'
        }
    },


    computed: {

        /* =====================================================
         * TITLE WORD COUNT
         * =================================================== */

        titleWordCount() {

            const text =
                this.title
                    .replace(/\s+/g, ' ')
                    .trim()

            return text
                ? text.split(/\s+/).length
                : 0
        },


        /* =====================================================
         * TITLE CHARACTER COUNT
         * =================================================== */

        titleCharCount() {

            return this.title.length
        },


        /* =====================================================
         * CONTENT STATISTICS
         * =================================================== */

        statistics() {

            const html =
                this.description || ''

            const parser =
                new DOMParser()

            const doc =
                parser.parseFromString(
                    html,
                    'text/html'
                )

            const text =
                (doc.body.innerText || '')
                    .replace(/\s+/g, ' ')
                    .trim()

            const words =
                text
                    ? text.split(/\s+/).length
                    : 0

            const characters =
                text.length

            const paragraphs =
                doc.querySelectorAll(
                    'p'
                ).length

            const headings =
                doc.querySelectorAll(
                    'h1, h2, h3, h4, h5, h6'
                ).length

            const links =
                doc.querySelectorAll(
                    'a'
                ).length

            const images =
                doc.querySelectorAll(
                    'img'
                ).length

            const wordsPerMinute =
                200

            const minutes =
                words > 0
                    ? Math.max(
                        1,
                        Math.ceil(
                            words /
                            wordsPerMinute
                        )
                    )
                    : 0

            return {

                words,

                characters,

                paragraphs,

                headings,

                links,

                images,

                readingTime:
                    minutes > 0
                        ? `~${minutes} min read`
                        : '0 min read'
            }
        }
    },


    /* =========================================================
       WATCHERS
    ========================================================== */

    watch: {

        /*
         * Title changes.
         */
        title() {

            this.markAsChanged()
        },

        /*
         * Status changes.
         */
        status() {

            this.markAsChanged()
        }
    },


    /* =========================================================
       MOUNTED
    ========================================================== */

    mounted() {

        this.initializeEditor()
    },


    /* =========================================================
       BEFORE UNMOUNT
    ========================================================== */

    beforeUnmount() {

        clearTimeout(
            this.autosaveTimer
        )

        if (this.autosaveRequest) {

            this.autosaveRequest.abort()
        }

        if (window.tinymce) {

            const editor =
                tinymce.get('editor')

            if (editor) {

                editor.remove()
            }
        }
    },


    methods: {

        /* =====================================================
         * TINYMCE INITIALIZATION
         * =================================================== */

        initializeEditor() {

            if (!window.tinymce) {

                console.error(
                    'TinyMCE is not loaded.'
                )

                return
            }

            const existingEditor =
                tinymce.get('editor')

            if (existingEditor) {

                existingEditor.remove()
            }

            tinymce.init({

                selector: '#editor',

                height: 350,

                menubar: true,

                plugins:
                    'lists link image table media fullscreen code codesample searchreplace',

                toolbar:
                    'undo redo | formatselect | ' +
                    'bold italic underline strikethrough | ' +
                    'alignleft aligncenter alignright alignjustify | ' +
                    'bullist numlist outdent indent | ' +
                    'link image table media | ' +
                    'searchreplace | ' +
                    'codesample code fullscreen',

                image_uploadtab: true,

                automatic_uploads: true,

                images_upload_handler:
                    (blobInfo, success, failure) => {

                        const reader =
                            new FileReader()

                        reader.onload =
                            () => {

                                success(
                                    reader.result
                                )
                            }

                        reader.onerror =
                            () => {

                                failure(
                                    'Image upload failed'
                                )
                            }

                        reader.readAsDataURL(
                            blobInfo.blob()
                        )
                    },

                setup: (editor) => {

                    /* =========================================
                       EDITOR READY
                    ========================================== */

                    editor.on(
                        'init',
                        () => {

                            editor.setContent(
                                this.description
                            )

                            /*
                             * Initial content should NOT
                             * trigger autosave.
                             */
                            this.saveStatus =
                                'saved'
                        }
                    )


                    /* =========================================
                       EDITOR CONTENT CHANGED
                    ========================================== */

                    editor.on(
                        'Change KeyUp Undo Redo Input',
                        () => {

                            this.description =
                                editor.getContent()

                            this.markAsChanged()
                        }
                    )
                }
            })
        },


        /* =====================================================
         * MARK CHANGES
         * =================================================== */

        markAsChanged() {

            /*
             * Immediately show:
             *
             * 🟡 Unsaved changes
             */

            this.saveStatus =
                'unsaved'

            this.lastSaved =
                ''

            /*
             * Reset previous timer.
             */

            clearTimeout(
                this.autosaveTimer
            )

            /*
             * Wait 2 seconds.
             */

            this.autosaveTimer =
                setTimeout(
                    () => {

                        this.saveDraft()

                    },
                    2000
                )
        },


        /* =====================================================
         * AUTOSAVE
         * =================================================== */

        saveDraft() {

            /*
             * Show:
             *
             * 🔵 Saving...
             */

            this.saveStatus =
                'saving'

            /*
             * Get latest TinyMCE content.
             */

            const editor =
                this.getEditor()

            if (editor) {

                this.description =
                    editor.getContent()
            }

            /*
             * Abort previous request if
             * one is still running.
             */

            if (this.autosaveRequest) {

                this.autosaveRequest.abort()
            }

            const controller =
                new AbortController()

            this.autosaveRequest =
                controller

            const formData =
                new FormData()

            formData.append(
                '_token',
                this.csrf
            )

            formData.append(
                'title',
                this.title
            )

            formData.append(
                'description',
                this.description
            )

            formData.append(
                'status',
                this.status
            )

            fetch(
                `/autosave/${this.item.id}`,
                {

                    method: 'POST',

                    body: formData,

                    signal:
                        controller.signal,

                    headers: {

                        'X-Requested-With':
                            'XMLHttpRequest',

                        'Accept':
                            'application/json'
                    }
                }
            )

                .then(response => {

                    if (!response.ok) {

                        throw new Error(
                            'Autosave failed'
                        )
                    }

                    return response.json()
                })

                .then(data => {

                    /*
                     * Only show saved when
                     * Laravel confirms success.
                     */

                    this.saveStatus =
                        'saved'

                    this.lastSaved =
                        'Saved at ' +
                        new Date()
                            .toLocaleTimeString()

                    this.autosaveRequest =
                        null
                })

                .catch(error => {

                    /*
                     * Ignore abort errors.
                     */

                    if (
                        error.name ===
                        'AbortError'
                    ) {
                        return
                    }

                    console.error(
                        'Autosave error:',
                        error
                    )

                    this.saveStatus =
                        'error'

                    this.lastSaved =
                        ''

                    this.autosaveRequest =
                        null
                })
        },


        /* =====================================================
         * FORM SUBMIT
         * =================================================== */

        submitForm() {

            /*
             * Cancel pending autosave.
             */

            clearTimeout(
                this.autosaveTimer
            )

            /*
             * Get latest editor content.
             */

            const editor =
                this.getEditor()

            if (editor) {

                this.description =
                    editor.getContent()
            }

            const form =
                this.$refs.editForm

            /*
             * Remove old dynamic
             * description fields.
             */

            const oldInputs =
                form.querySelectorAll(
                    '.dynamic-description'
                )

            oldInputs.forEach(
                input => input.remove()
            )

            /*
             * Add latest description.
             */

            const hiddenInput =
                document.createElement(
                    'input'
                )

            hiddenInput.type =
                'hidden'

            hiddenInput.name =
                'description'

            hiddenInput.value =
                this.description

            hiddenInput.className =
                'dynamic-description'

            form.appendChild(
                hiddenInput
            )

            /*
             * Normal POST request.
             */

            form.submit()
        },


        /* =====================================================
         * FIND & REPLACE
         * =================================================== */

        openFindReplace() {

            this.findText = ''

            this.replaceText = ''

            this.findMessage = ''

            this.findMessageType =
                'success'

            this.showFindReplace =
                true

            this.$nextTick(() => {

                const input =
                    document.querySelector(
                        'input[placeholder="Enter text to find"]'
                    )

                if (input) {

                    input.focus()
                }
            })
        },


        closeFindReplace() {

            this.showFindReplace =
                false

            this.findText = ''

            this.replaceText = ''

            this.findMessage = ''
        },


        getEditor() {

            if (!window.tinymce) {

                return null
            }

            return tinymce.get(
                'editor'
            )
        },


        /* =====================================================
         * REPLACE FIRST
         * =================================================== */

        replaceFirst() {

            if (
                !this.findText ||
                !this.findText.trim()
            ) {

                this.showFindMessage(
                    'Please enter text to find.',
                    'warning'
                )

                return
            }

            const editor =
                this.getEditor()

            if (!editor) {

                this.showFindMessage(
                    'Editor is not ready.',
                    'warning'
                )

                return
            }

            const content =
                editor.getContent()

            const index =
                content.indexOf(
                    this.findText
                )

            if (index === -1) {

                this.showFindMessage(
                    'Text not found.',
                    'warning'
                )

                return
            }

            const updatedContent =
                content.substring(
                    0,
                    index
                ) +
                this.replaceText +
                content.substring(
                    index +
                    this.findText.length
                )

            editor.setContent(
                updatedContent
            )

            this.description =
                editor.getContent()

            /*
             * markAsChanged() will be
             * triggered by TinyMCE too.
             */

            this.markAsChanged()

            this.showFindMessage(
                'First occurrence replaced successfully.',
                'success'
            )
        },


        /* =====================================================
         * REPLACE ALL
         * =================================================== */

        replaceAll() {

            if (
                !this.findText ||
                !this.findText.trim()
            ) {

                this.showFindMessage(
                    'Please enter text to find.',
                    'warning'
                )

                return
            }

            const editor =
                this.getEditor()

            if (!editor) {

                this.showFindMessage(
                    'Editor is not ready.',
                    'warning'
                )

                return
            }

            const content =
                editor.getContent()

            const occurrences =
                content
                    .split(
                        this.findText
                    )
                    .length - 1

            if (occurrences === 0) {

                this.showFindMessage(
                    'Text not found.',
                    'warning'
                )

                return
            }

            const updatedContent =
                content
                    .split(
                        this.findText
                    )
                    .join(
                        this.replaceText
                    )

            editor.setContent(
                updatedContent
            )

            this.description =
                editor.getContent()

            this.markAsChanged()

            this.showFindMessage(
                `${occurrences} occurrence(s) replaced successfully.`,
                'success'
            )
        },


        showFindMessage(
            message,
            type
        ) {

            this.findMessage =
                message

            this.findMessageType =
                type
        },


        /* =====================================================
         * COMMENTS
         * =================================================== */

        addComment() {

            if (
                !this.newComment.trim()
            ) {

                return
            }

            const formData =
                new FormData()

            formData.append(
                '_token',
                this.csrf
            )

            formData.append(
                'body',
                this.newComment
            )

            formData.append(
                'user_name',
                'User'
            )

            fetch(
                `/comment/${this.item.id}`,
                {

                    method: 'POST',

                    body: formData,

                    headers: {

                        'X-Requested-With':
                            'XMLHttpRequest',

                        'Accept':
                            'application/json'
                    }
                }
            )

                .then(response => {

                    if (!response.ok) {

                        throw new Error(
                            'Comment failed'
                        )
                    }

                    return response.json()
                })

                .then(() => {

                    this.newComment =
                        ''

                    window.location.reload()
                })

                .catch(error => {

                    console.error(
                        error
                    )

                    alert(
                        'Unable to add comment.'
                    )
                })
        },


        /* =====================================================
         * RESTORE REVISION
         * =================================================== */

        restoreRevision(rev) {

            if (
                !confirm(
                    'Restore this revision? The current content will be replaced.'
                )
            ) {

                return
            }

            this.title =
                rev.title

            this.description =
                rev.description || ''

            const editor =
                this.getEditor()

            if (editor) {

                editor.setContent(
                    this.description
                )
            }

            /*
             * Show unsaved changes.
             */

            this.markAsChanged()

            this.showFindMessage(
                'Revision restored in the editor. It will be auto-saved after 2 seconds.',
                'success'
            )
        },


        /* =====================================================
         * DATE
         * =================================================== */

        formatDate(date) {

            if (!date) {

                return ''
            }

            return new Date(
                date
            ).toLocaleString()
        }
    }
}

</script>


<style scoped>

.stat-box {

    border: 1px solid #dee2e6;

    border-radius: 10px;

    padding: 15px 10px;

    text-align: center;

    background: #fff;

    height: 100%;

    transition:
        transform 0.2s ease,
        box-shadow 0.2s ease;
}

.stat-box:hover {

    transform:
        translateY(-2px);

    box-shadow:
        0 4px 12px rgba(
            0,
            0,
            0,
            0.08
        );
}

.stat-icon {

    font-size: 24px;

    margin-bottom: 5px;
}

.stat-value {

    font-size: 22px;

    font-weight: 700;
}

.stat-label {

    font-size: 13px;

    color: #6c757d;
}


/* =========================================================
   AUTOSAVE STATUS
========================================================= */

.autosave-status {

    min-height: 24px;

    display: flex;

    align-items: center;

    flex-wrap: wrap;
}

.status-item {

    display: inline-flex;

    align-items: center;

    gap: 6px;

    font-size: 14px;

    font-weight: 600;
}


/* =========================================================
   UNSAVED
========================================================= */

.status-unsaved {

    color: #856404;
}


/* =========================================================
   SAVING
========================================================= */

.status-saving {

    color: #0d6efd;
}


/* =========================================================
   SAVED
========================================================= */

.status-saved {

    color: #198754;
}


/* =========================================================
   ERROR
========================================================= */

.status-error {

    color: #dc3545;
}


/* =========================================================
   STATUS DOT
========================================================= */

.status-dot {

    width: 9px;

    height: 9px;

    border-radius: 50%;

    display: inline-block;

    background: currentColor;
}

</style>