<template>
  <div>
  <h1>Choose a plan below and subscribe for it.</h1>
  <form method="POST" @submit.prevent="subscribe">
     <input type="hidden" name="stripeToken" v-model="stripeToken">
     <input type="hidden" name="stripeEmail" v-model="stripeEmail">
     <select name="plan" v-model="plan">
        <option v-for="plan in plans" :value="plan.id">
            {{plan.name}}&mdash;${{plan.price}}
        </option>
     </select>

     <div class="form-group">
        <input type="text" name="coupon" placeholder="Have a coupon code?" class="form-control" v-model="coupon">
     </div>

     <div class="form-group">
       <button class="btn btn-primary" type="submit">Subscribe</button>
     </div>

     <p class="help is-danger" v-show="status" v-text="status"></p>
  </form>
  </div>
</template>

<script>
export default {
        props: ['plans'],
        data() {
           return {
              stripeEmail: '',
              stripeToken: '',
              plan: 1,
              status: false,
              coupon: ''
           };
        },
        created() {
          let selected = this;
          this.stripe = StripeCheckout.configure({
              key: Laravel.stripeKey,
              image: "https://stripe.com/img/documentation/checkout/marketplace.png",
              locale: "auto",
              panelLabel: "Subscribe for",
              email: Laravel.user.email,
              token: (token) => {
                axios.post('/subscriptions', {
                    stripeEmail: token.email,
                    stripeToken: token.id,
                    plan: selected.plan,
                    coupon: selected.coupon
                }).then(response => this.status = 'Payment Successful !').catch(error => this.status = error.response.data.status);
              }
           });
        },
        methods: {
          subscribe() {
             let plan = this.findPlanById(this.plan);
             this.stripe.open({
                    name: plan.name,
                    description: plan.name,
                    zipCode: true,
                    amount: plan.price * 100
                });
            },
            findPlanById(id) {
               return this.plans.find(plan => plan.id == id);
            }
      }
}
</script>