<template>
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">{{ isEdit ? 'Edit Title' : 'Create Title' }}</h4>
            <div>
                <button type="button" class="btn btn-outline-secondary btn-sm me-1" @click="showPreview = true">Preview</button>
                <a href="/" class="btn btn-secondary btn-sm">Back</a>
            </div>
        </div>

        <div class="card-body">
            <form ref="createForm" :action="formAction" method="POST" @submit.prevent="submitForm">
                <input type="hidden" name="_token" :value="csrf">
                <input v-if="isEdit" type="hidden" name="_method" value="PUT">

                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input
                        type="text"
                        name="title"
                        v-model="title"
                        class="form-control"
                        placeholder="Enter title"
                        required
                    >
                    <div class="form-text">
                        Words: {{ wordCount }} | Characters: {{ charCount }}
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" v-model="status">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea id="editor"></textarea>
                    <input type="hidden" name="description" :value="description">
                    <div class="form-text d-flex justify-content-between">
                        <span>Auto-saved: {{ lastSaved }}</span>
                        <span v-if="saving" class="text-primary">Saving...</span>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn" :class="isEdit ? 'btn-primary' : 'btn-success'">
                        {{ isEdit ? 'Update' : 'Save' }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Preview Modal -->
        <div v-if="showPreview" class="modal fade show d-block" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Preview</h5>
                        <button type="button" class="btn-close" @click="showPreview = false"></button>
                    </div>
                    <div class="modal-body">
                        <h4>{{ title }}</h4>
                        <div v-html="description"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="showPreview = false">Close</button>
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
        const item = el.dataset.item ? JSON.parse(el.dataset.item) : null
        return {
            title: item ? item.title : '',
            description: item ? item.description : '',
            status: item ? item.status : 'draft',
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            isEdit: !!item,
            saving: false,
            lastSaved: 'Never',
            showPreview: false,
            autosaveTimer: null
        }
    },
    computed: {
        formAction() {
            return this.isEdit ? `/update/${JSON.parse(document.getElementById('app').dataset.item).id}` : '/store'
        },
        wordCount() {
            const text = this.title.replace(/<[^>]*>/g, '')
            return text.trim() ? text.trim().split(/\s+/).length : 0
        },
        charCount() {
            return this.title.replace(/<[^>]*>/g, '').length
        }
    },
    mounted() {
        tinymce.init({
            selector: '#editor',
            height: 350,
            menubar: true,
            plugins: 'lists link image table media fullscreen code codesample',
            toolbar: 'undo redo | formatselect | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image table media | codesample code fullscreen',
            image_uploadtab: true,
            automatic_uploads: true,
            images_upload_handler: (blobInfo, success, failure) => {
                const reader = new FileReader()
                reader.onload = () => success(reader.result)
                reader.onerror = failure
                reader.readAsDataURL(blobInfo.blob())
            },
            setup: (editor) => {
                editor.on('init', () => {
                    if (this.description) {
                        editor.setContent(this.description)
                    }
                })
                editor.on('Change KeyUp', () => {
                    this.description = editor.getContent()
                    this.triggerAutosave()
                })
            }
        })

        window.addEventListener('beforeunload', () => {
            if (this.description) this.saveDraft()
        })
    },
    methods: {
        submitForm() {
            const form = this.$refs.createForm
            const hiddenInput = document.createElement('input')
            hiddenInput.type = 'hidden'
            hiddenInput.name = 'description'
            hiddenInput.value = this.description
            form.appendChild(hiddenInput)
            form.submit()
        },
        triggerAutosave() {
            if (!this.isEdit) return
            this.saving = true
            clearTimeout(this.autosaveTimer)
            this.autosaveTimer = setTimeout(() => {
                this.saveDraft()
            }, 2000)
        },
        saveDraft() {
            if (!this.isEdit) return
            const item = JSON.parse(document.getElementById('app').dataset.item)
            const formData = new FormData()
            formData.append('_token', this.csrf)
            formData.append('title', this.title)
            formData.append('description', this.description)
            formData.append('status', this.status)

            fetch(`/autosave/${item.id}`, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => {
                if (!res.ok) throw new Error('Autosave failed')
                return res.json()
            })
            .then(() => {
                this.saving = false
                this.lastSaved = new Date().toLocaleTimeString()
            })
            .catch(() => {
                this.saving = false
            })
        }
    }
}
</script>
