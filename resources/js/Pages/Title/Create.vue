<template>
    <div class="card shadow-sm">
        <!-- Header -->
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">
                {{ isEdit ? 'Edit Title' : 'Create Title' }}
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

                <a href="/" class="btn btn-secondary btn-sm">
                    Back
                </a>
            </div>
        </div>

        <div class="card-body">

            <!-- Editor Form -->
            <form
                ref="createForm"
                :action="formAction"
                method="POST"
                @submit.prevent="submitForm"
            >
                <input
                    type="hidden"
                    name="_token"
                    :value="csrf"
                >

                <input
                    v-if="isEdit"
                    type="hidden"
                    name="_method"
                    value="PUT"
                >

                <!-- Title -->
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
                </div>

                <!-- Status -->
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

                <!-- Description -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Description
                    </label>

                    <textarea id="editor"></textarea>

                    <input
                        type="hidden"
                        name="description"
                        :value="description"
                    >

                    <!-- Autosave Status -->
                    <div class="form-text d-flex justify-content-between mt-2">
                        <span>
                            <span v-if="saving" class="text-primary">
                                Saving...
                            </span>

                            <span v-else>
                                Auto-saved:
                                {{ lastSaved }}
                            </span>
                        </span>

                        <span class="text-muted">
                            Content updates automatically
                        </span>
                    </div>
                </div>

                <!-- Content Statistics -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
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

                            <!-- Words -->
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

                            <!-- Characters -->
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

                            <!-- Paragraphs -->
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

                            <!-- Headings -->
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

                            <!-- Links -->
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

                            <!-- Images -->
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

                <!-- Actions -->
                <div class="d-flex gap-2">
                    <button
                        type="submit"
                        class="btn"
                        :class="isEdit ? 'btn-primary' : 'btn-success'"
                    >
                        {{ isEdit ? 'Update' : 'Save' }}
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

        <!-- Find & Replace Modal -->
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

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Find
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                v-model="findText"
                                placeholder="Enter text to find"
                            >
                        </div>

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

                        <div
                            v-if="findMessage"
                            class="alert"
                            :class="findMessageType === 'success'
                                ? 'alert-success'
                                : 'alert-warning'"
                        >
                            {{ findMessage }}
                        </div>

                        <div class="small text-muted">
                            You can replace the first match or replace all
                            matching text in the editor.
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

        <!-- Preview Modal -->
        <div
            v-if="showPreview"
            class="modal fade show d-block"
            tabindex="-1"
            style="background: rgba(0, 0, 0, 0.5);"
        >
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
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
                                'bg-success': status === 'published',
                                'bg-warning text-dark': status === 'draft',
                                'bg-secondary': status === 'archived'
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
    </div>
</template>

<script>
export default {
    data() {
        const el = document.getElementById('app')

        const item = el.dataset.item
            ? JSON.parse(el.dataset.item)
            : null

        return {
            title: item ? item.title : '',
            description: item ? item.description : '',
            status: item ? item.status : 'draft',

            item: item,

            csrf: document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute('content'),

            isEdit: !!item,

            saving: false,
            lastSaved: 'Never',

            showPreview: false,

            autosaveTimer: null,

            /* Find & Replace */
            showFindReplace: false,
            findText: '',
            replaceText: '',
            findMessage: '',
            findMessageType: 'success'
        }
    },

    computed: {
        formAction() {
            return this.isEdit
                ? `/update/${this.item.id}`
                : '/store'
        },

        statistics() {
            const html = this.description || ''

            const parser = new DOMParser()
            const doc = parser.parseFromString(html, 'text/html')

            const text = (doc.body.innerText || '')
                .replace(/\s+/g, ' ')
                .trim()

            const words = text
                ? text.split(/\s+/).length
                : 0

            const characters = text.length

            const paragraphs = doc.querySelectorAll('p').length

            const headings = doc.querySelectorAll(
                'h1, h2, h3, h4, h5, h6'
            ).length

            const links = doc.querySelectorAll('a').length

            const images = doc.querySelectorAll('img').length

            const wordsPerMinute = 200

            const minutes = words > 0
                ? Math.max(1, Math.ceil(words / wordsPerMinute))
                : 0

            return {
                words,
                characters,
                paragraphs,
                headings,
                links,
                images,
                readingTime: minutes > 0
                    ? `~${minutes} min read`
                    : '0 min read'
            }
        }
    },

    mounted() {
        this.initializeEditor()

        window.addEventListener(
            'beforeunload',
            this.handleBeforeUnload
        )
    },

    beforeUnmount() {
        clearTimeout(this.autosaveTimer)

        window.removeEventListener(
            'beforeunload',
            this.handleBeforeUnload
        )

        if (window.tinymce) {
            const editor = tinymce.get('editor')

            if (editor) {
                editor.remove()
            }
        }
    },

    methods: {

        /* =========================================================
         * TINYMCE INITIALIZATION
         * ========================================================= */

        initializeEditor() {
            tinymce.init({
                selector: '#editor',

                height: 350,

                menubar: true,

                plugins:
                    'lists link image table media fullscreen code codesample searchreplace',

                toolbar:
                    'undo redo | formatselect | bold italic underline strikethrough | ' +
                    'alignleft aligncenter alignright alignjustify | ' +
                    'bullist numlist outdent indent | ' +
                    'link image table media | ' +
                    'searchreplace | codesample code fullscreen',

                image_uploadtab: true,

                automatic_uploads: true,

                images_upload_handler: (blobInfo, success, failure) => {
                    const reader = new FileReader()

                    reader.onload = () => {
                        success(reader.result)
                    }

                    reader.onerror = () => {
                        failure('Image upload failed')
                    }

                    reader.readAsDataURL(blobInfo.blob())
                },

                setup: (editor) => {

                    editor.on('init', () => {
                        if (this.description) {
                            editor.setContent(this.description)
                        }
                    })

                    editor.on(
                        'Change KeyUp Undo Redo',
                        () => {
                            this.description =
                                editor.getContent()

                            this.triggerAutosave()
                        }
                    )
                }
            })
        },

        /* =========================================================
         * FORM SUBMIT
         * ========================================================= */

        submitForm() {
            if (window.tinymce) {
                const editor = tinymce.get('editor')

                if (editor) {
                    this.description =
                        editor.getContent()
                }
            }

            const form = this.$refs.createForm

            const hiddenInput =
                document.createElement('input')

            hiddenInput.type = 'hidden'
            hiddenInput.name = 'description'
            hiddenInput.value = this.description

            form.appendChild(hiddenInput)

            form.submit()
        },

        /* =========================================================
         * AUTOSAVE
         * ========================================================= */

        triggerAutosave() {
            if (!this.isEdit) {
                return
            }

            this.saving = true

            clearTimeout(this.autosaveTimer)

            this.autosaveTimer = setTimeout(() => {
                this.saveDraft()
            }, 2000)
        },

        saveDraft() {
            if (!this.isEdit) {
                return
            }

            const formData = new FormData()

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

            fetch(`/autosave/${this.item.id}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(
                            'Autosave failed'
                        )
                    }

                    return response.json()
                })
                .then(() => {
                    this.saving = false

                    this.lastSaved =
                        new Date().toLocaleTimeString()
                })
                .catch(() => {
                    this.saving = false

                    this.lastSaved =
                        'Save failed'
                })
        },

        handleBeforeUnload() {
            if (this.isEdit && this.description) {
                this.saveDraft()
            }
        },

        /* =========================================================
         * FIND & REPLACE
         * ========================================================= */

        openFindReplace() {
            this.findMessage = ''
            this.findText = ''
            this.replaceText = ''

            this.showFindReplace = true
        },

        closeFindReplace() {
            this.showFindReplace = false

            this.findMessage = ''
            this.findText = ''
            this.replaceText = ''
        },

        getEditor() {
            if (!window.tinymce) {
                return null
            }

            return tinymce.get('editor')
        },

        replaceFirst() {
            if (!this.findText.trim()) {
                this.showFindMessage(
                    'Please enter text to find.',
                    'warning'
                )

                return
            }

            const editor = this.getEditor()

            if (!editor) {
                this.showFindMessage(
                    'Editor is not ready.',
                    'warning'
                )

                return
            }

            const content = editor.getContent()

            const index = content.indexOf(
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
                content.substring(0, index) +
                this.replaceText +
                content.substring(
                    index + this.findText.length
                )

            editor.setContent(updatedContent)

            this.description =
                editor.getContent()

            this.triggerAutosave()

            this.showFindMessage(
                'First occurrence replaced successfully.',
                'success'
            )
        },

        replaceAll() {
            if (!this.findText.trim()) {
                this.showFindMessage(
                    'Please enter text to find.',
                    'warning'
                )

                return
            }

            const editor = this.getEditor()

            if (!editor) {
                this.showFindMessage(
                    'Editor is not ready.',
                    'warning'
                )

                return
            }

            const content = editor.getContent()

            const occurrences =
                content.split(this.findText).length - 1

            if (occurrences === 0) {
                this.showFindMessage(
                    'Text not found.',
                    'warning'
                )

                return
            }

            const updatedContent =
                content.split(this.findText)
                    .join(this.replaceText)

            editor.setContent(updatedContent)

            this.description =
                editor.getContent()

            this.triggerAutosave()

            this.showFindMessage(
                `${occurrences} occurrence(s) replaced successfully.`,
                'success'
            )
        },

        showFindMessage(message, type) {
            this.findMessage = message
            this.findMessageType = type
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
    transition: 0.2s ease;
}

.stat-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
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
</style>