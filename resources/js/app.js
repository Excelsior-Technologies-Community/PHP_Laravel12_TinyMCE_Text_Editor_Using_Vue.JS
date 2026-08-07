import { createApp } from 'vue'

import Index from './Pages/Title/Index.vue'
import Create from './Pages/Title/Create.vue'
import Edit from './Pages/Title/Edit.vue'
import Preview from './Pages/Title/Preview.vue'

const el = document.getElementById('app')

const page = el.dataset.page

let component = Index

if (page === 'create') component = Create
if (page === 'edit') component = Edit
if (page === 'preview') component = Preview

createApp(component).mount('#app')
