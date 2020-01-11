<template>
  <div>
    <p><span class="h4">Notes</span> &emsp; <button @click="view()">New</button></p>
    <p v-if="loadingNotes"><i>Loading...</i></p>
    <div class="scroll-card">
      <ul v-if="notes.length" class="list-group">
        <li 
          v-for="(note, i) in notes" 
          :key="note.id" 
          class="list-group-item d-flex justify-content-between align-items-center">
          {{ note.title }}
          <div class="btn-group btn-group-sm" role="group">
            <button
              type="button"
              class="btn btn-outline-secondary"
              @click.prevent.stop="view(i)">
              Edit
            </button>
            <button
              type="button"
              class="btn btn-outline-danger"
              @click.prevent.stop="deleteNote(i)">
              Delete
            </button>
          </div>
        </li>
      </ul>
      <h2 class="text-muted text-center" v-else>No notes</h2>
    </div>

    <div ref="modal" class="modal" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
              <inline-input 
                label-classes="modal-title h3"
                input-classes="form-control"
                :placeholder="titlePlaceholder" 
                v-model="note.title" />
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form>
              <div class="form-group row">
                <div class="col-sm-12 scroll-card">
                  <quill-editor v-model="note.text" />
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button
              :disabled="disableSaveButton"
              @click.stop.prevent="save()"
              type="button"
              class="btn btn-outline-secondary"
            >Save</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import 'quill/dist/quill.core.css';
import 'quill/dist/quill.snow.css';
import 'quill/dist/quill.bubble.css';
import { quillEditor } from 'vue-quill-editor';
import InlineInput from './InlineInput';

export default {
  name: 'Notes',
  components: {
    InlineInput,
    quillEditor
  },
  props: {
    contactId: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      notes: [],
      loadingNotes: false,
      busy: false,
      currNote: -1,
      lastSavedNote: '',
      note: {
        id: '',
        contact_id: this.contactId,
        title: '',
        text: ''
      }
    };
  },
  computed: {
    titlePlaceholder() {
      return this.note.title ? this.note.title : 'Add title...';
    },
    titleOk() {
      const { note: { id, title }, notes } = this;

      return !!title && notes.filter(n => n.id !== id).every(n => n.title !== title);
    },
    textOk() {
      return !!this.note.text;
    },
    noteChanged() {
      return this.lastSavedNote !== this.note.text.trim();
    },
    formOk() {
      return this.titleOk && this.textOk && this.noteChanged;
    },
    disableSaveButton() {
      return !this.formOk || this.busy;
    }
  },
  watch: {
    contactId: {
      immediate: true,
      handler() {
        this.note.contact_id = this.contactId;
        this.fetchNotes();
      }
    }
  },
  mounted() {
    this.scheduleNoteCheck();
  },
  methods: {
    scheduleNoteCheck() {
      setTimeout(this.checkNote, 5000);
    },
    checkNote() {
      if (this.note.id && this.lastSavedNote !== this.note.text.trim()) {
        this.save();
      }

      this.scheduleNoteCheck();
    },
    fetchNotes() {
      if (this.loadingNotes) return;

      this.notes = [];
      this.loadingNotes = true;

      axios.get(`/api/contacts/${this.contactId}/notes`)
        .then(({ data: { data } }) => {
          this.notes = data;
          this.loadingNotes = false;
        })
        .catch(() => {
          this.loadingNotes = false;
        });
    },
    deleteNote(index) {
      if (!confirm('Are you sure?')) return;

      axios.delete(`/api/notes/${this.notes[index].id}`)
        .then(() => {
          this.notes.splice(index, 1);
        })
        .catch(() => {
          alert('Could not delete');
        });
    },
    save() {
      if (this.busy || !this.formOk) return;

      this.note.title = this.note.title.trim();
      this.note.text = this.note.text.trim();
      this.busy = true;

      if (this.note.id) {
        axios.put(`/api/notes/${this.note.id}`, this.note)
          .then(({ data: { data } }) => {
            this.busy = false;
            this.notes[this.currNote] = data;
            this.lastSavedNote = this.note.text;
          })
          .catch(() => {
            this.busy = false;
            alert('Could not update');
          });
      } else {
        axios.post('/api/notes', this.note)
          .then(({ data: { data } }) => {
            this.busy = false;
            this.lastSavedNote = this.note.text;

            this.notes.push(data);
          })
          .catch(() => {
            this.busy = false;
            alert('Could not create');
          });
      }
    },
    view(index = -1) {
      this.currNote = index;
      
      if (index !== -1) {
        this.note = { ...this.notes[this.currNote] };
        this.lastSavedNote = this.note.text;
      } else {
        this.resetNote();
      }

      $(this.$refs.modal).modal('show');
    },
    resetNote() {
      this.note.id = '';
      this.note.text = '';
      this.note.title = '';
      this.lastSavedNote = '';
      this.currNote = -1;
    }
  }
}
</script>