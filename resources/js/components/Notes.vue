<template>
  <div>
    <p v-if="loadingNotes"><i>Loading...</i></p>
    
    <p>
      <InlineInput 
        label-classes="modal-title h3"
        input-classes="form-control"
        :placeholder="titlePlaceholder" 
        v-model="note.title" />
    </p>

    <div class="scroll-card">
      <quill-editor v-model="note.text" />

      <button
        :disabled="disableSaveButton"
        @click.stop.prevent="save()"
        type="button"
        class="btn btn-outline-secondary mt-2"
      >Save</button>
    </div>

  </div>
</template>

<script>
import 'quill/dist/quill.core.css';
import 'quill/dist/quill.snow.css';
import 'quill/dist/quill.bubble.css';
import { quillEditor } from 'vue-quill-editor';
import InlineInput from 'vue-inline-input';

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
      if (this.note.id && this.lastSavedNote !== this.note.text.trim()) this.save();

      this.scheduleNoteCheck();
    },
    fetchNotes() {
      this.notes = [];
      this.loadingNotes = true;

      axios.get(`/api/contacts/${this.contactId}/notes`)
        .then(({ data: { data } }) => {
          this.loadingNotes = false;
          this.notes = data;

          if (this.notes.length) {
            this.currNote = 0;
            this.note = this.notes[this.currNote];
          }          
        })
        .catch(() => {
          this.loadingNotes = false;
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
    }
  }
}
</script>