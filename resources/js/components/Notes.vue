<template>
  <div>
    <p><span class="h4">Notes</span> &emsp; <button @click="view(null)">Add</button></p>
    <p v-if="loadingNotes"><i>Loading...</i></p>
    <ul v-if="notes.length" class="list-group">
      <li v-for="note in notes" :key="note.id" class="list-group-item d-flex justify-content-between align-items-center">
        {{ note.title }}
        <div class="btn-group btn-group-sm" role="group">
          <button
            type="button"
            class="btn btn-outline-secondary"
            @click.prevent.stop="view(note)">
            Edit
          </button>
          <button
            type="button"
            class="btn btn-outline-danger"
            @click.prevent.stop="deleteNote(note.id)">
            Delete
          </button>
        </div>
      </li>
    </ul>
    <h2 class="text-muted text-center" v-else>No notes</h2>

    <div ref="modal" class="modal" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ modalTitle }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form>
              <div class="form-group row">
                <label class="control-label col-sm-12">Title</label>
                <div class="col-sm-12">
                  <input type="text" class="form-control" v-model="note.title">
                </div>
              </div>
              <div class="form-group row">
                <label class="control-label col-sm-12">Text</label>
                <div class="col-sm-12">
                  <vue-trix v-model="note.text" placeholder="Enter note" />
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button
              :disabled="!formOk"
              @click.stop.prevent="save()"
              type="button"
              class="btn btn-secondary"
            >Save</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import VueTrix from 'vue-trix';

export default {
  name: 'Notes',
  components: {
    VueTrix
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
      note: {
        id: '',
        contact_id: this.contactId,
        title: '',
        text: ''
      }
    };
  },
  computed: {
    modalTitle() {
      return !!this.note.id ? 'Update note' : 'Add note';
    },
    titleOk() {
      const { note: { id, title }, notes } = this;

      return !!title && notes.filter(n => n.id !== id).every(n => n.title !== title);
    },
    textOk() {
      return !!this.note.text;
    },
    formOk() {
      return this.titleOk && this.textOk;
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
  methods: {
    fetchNotes() {
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
    deleteNote(noteId) {
      if (!confirm('Are you sure?')) return;

      axios.delete(`/api/notes/${noteId}`)
        .then(() => {
          this.notes = this.notes.filter(({ id }) => noteId !== id);
        })
        .catch(() => {
          alert('Could not delete');
        });
    },
    save() {
      this.note.title = this.note.title.trim();
      this.note.text = this.note.text.trim();

      if (this.note.id) {
        axios.put(`/api/notes/${this.note.id}`, this.note)
          .then(({ data: { data } }) => {
            $(this.$refs.modal).modal('hide');

            this.notes = this.notes.map(n => n.id === this.note.id ? data : n);

            this.resetNote();
          })
          .catch(() => {
            alert('Could not update');
          });
      } else {
        axios.post('/api/notes', this.note)
          .then(({ data: { data } }) => {
            this.notes.push(data);

            $(this.$refs.modal).modal('hide');
          })
          .catch(() => {
            alert('Could not create');
          });
      }
    },
    view(note) {
      if (note) {
        this.note = { ...note };
      } else {
        this.resetNote();
      }
      $(this.$refs.modal).modal('show');
    },
    resetNote() {
      this.note.id = '';
      this.note.text = '';
      this.note.title = '';
    }
  }
}
</script>
