<template>
  <div>
    <p><span class="h4">Addresses</span> &emsp; <button @click="view(null)">Add</button></p>
    <p v-if="loadingAddresses"><i>Loading...</i></p>
    <ul v-if="addresses.length" class="list-group">
      <li v-for="address in addresses" :key="address.id" class="list-group-item d-flex justify-content-between align-items-center">
        <span class="badge badge-secondary">{{ address.key | keyLabel }}</span>
        {{ address.value }}
        <div class="btn-group btn-group-sm" role="group">
          <button
            type="button"
            class="btn btn-outline-secondary"
            @click.prevent.stop="view(address)">
            Edit
          </button>
          <button
            type="button"
            class="btn btn-outline-danger"
            @click.prevent.stop="deleteAddress(address.id)">
            Delete
          </button>
        </div>
      </li>
    </ul>
    <h2 class="text-muted text-center" v-else>No addresses</h2>

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
                <label class="control-label col-sm-12">Type</label>
                <div class="col-sm-12">
                  <select v-model="address.key" id="" class="form-control">
                    <option value="phone">Phone</option>
                    <option value="email">Email</option>
                    <option value="physical">Address</option>
                  </select>
                </div>
              </div>
              <div class="form-group row">
                <label class="control-label col-sm-12">Value</label>
                <div class="col-sm-12">
                  <input type="text" class="form-control" v-model="address.value" :placeholder="placeholder">
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
export default {
  name: 'Addresses',
  props: {
    contactId: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      addresses: [],
      loadingAddresses: false,
      address: {
        id: '',
        contact_id: this.contactId,
        key: 'phone',
        value: ''
      }
    };
  },
  computed: {
    modalTitle() {
      return !!this.address.id ? 'Update contact detail' : 'Add contact detail';
    },
    placeholder() {
      if (this.address.key === 'phone') return 'Enter phone number';

      if (this.address.key === 'email') return 'Enter email address';

      if (this.address.key === 'physical') return 'Enter address';
    },
    keyOk() {
      return ['email', 'phone', 'physical'].indexOf(this.address.key) !== -1;
    },
    valueOk() {
      const { address: { id, value, key }, addresses } = this;

      return !!value &&
        addresses
          .filter(a => a.id !== id && a.key === key)
          .every(a => a.value !== value);
    },
    formatOk() {
      if (this.address.key === 'email') return /^\w+\.*\w*@\w+\.\w+/.test(this.address.value);

      if (this.address.key === 'phone') return /^[+]?\d+$/.test(this.address.value);

      if (this.address.key === 'physical') return !!this.address.value;
    },
    formOk() {
      return this.keyOk && this.valueOk && this.formatOk;
    }
  },
  watch: {
    contactId: {
      immediate: true,
      handler() {
        this.address.contact_id = this.contactId;
        this.fetchAddresses();
      }
    }
  },
  filters: {
    keyLabel(key) {
      if (key === 'physical') return 'Address';

      return `${key[0].toUpperCase()}${key.substr(1)}`;
    }
  },
  methods: {
    fetchAddresses() {
      this.addresses = [];
      this.loadingAddresses = true;

      axios.get(`/api/contacts/${this.contactId}/addresses`)
        .then(({ data: { data } }) => {
          this.addresses = data;
          this.loadingAddresses = false;
        })
        .catch(() => {
          this.loadingAddresses = false;
        });
    },
    deleteAddress(addressId) {
      if (!confirm('Are you sure?')) return;

      axios.delete(`/api/addresses/${addressId}`)
        .then(() => {
          this.addresses = this.addresses.filter(({ id }) => addressId !== id);
        })
        .catch(() => {
          alert('Could not delete');
        });
    },
    save() {
      this.address.key = this.address.key.trim();
      this.address.value = this.address.value.trim();

      if (this.address.id) {
        axios.put(`/api/addresses/${this.address.id}`, this.address)
          .then(({ data: { data } }) => {
            $(this.$refs.modal).modal('hide');

            this.addresses = this.addresses.map(a => a.id === this.address.id ? data : a);

            this.resetAddress();
          })
          .catch(() => {
            alert('Could not update');
          });
      } else {
        axios.post('/api/addresses', this.address)
          .then(({ data: { data } }) => {
            this.addresses.push(data);

            $(this.$refs.modal).modal('hide');
          })
          .catch(() => {
            alert('Could not create');
          });
      }
    },
    view(address) {
      if (address) {
        this.address = { ...address };
      } else {
        this.resetAddress();
      }
      $(this.$refs.modal).modal('show');
    },
    resetAddress() {
      this.address.id = '';
      this.address.value = '';
      this.address.key = 'phone';
    }
  }
}
</script>
