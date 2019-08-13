$( document ).ready(function() {
    const currencyFormatter = new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN',
        minimumFractionDigits: 2
    });

    var app = new Vue({
        el: '#app',
        data: {
            amount: 0,
            paymentAmount: 0,
            interval: null
        },
        computed: {
            formatedAmount: function() {
                console.log('formater', this.amount)
                return currencyFormatter.format(this.amount)
            }
        },
        mounted: async function() {
            let amount = 0
            try {
                let balance = await fetch(siteUrl+'/balance', {
                    method: 'GET'
                })
                let balanceData = await balance.json()
                console.log([balanceData, balanceData['L_AMT0']])
                if (balanceData && balanceData['L_AMT0']) {
                    amount = parseFloat(balanceData['L_AMT0'])
                }
            } catch (error) {

            }
            this.amount = amount
            console.log('mounted', this.amount)
            return this.amount
        },
        methods: {
            balance: async function() {
                try {
                    let balance = await fetch(siteUrl+'/balance', {
                        method: 'GET'
                    })
                    let balanceData = await balance.json()
                    if (balanceData && balanceData['L_AMT0']) {
                        if(this.amount !== parseFloat(balanceData['L_AMT0'])){
                            this.amount = parseFloat(balanceData['L_AMT0'])
                            this.paymentAmount = 0
                            clearInterval(this.interval)
                        }
                    }
                } catch (error) {

                }
            },
            payment: async function() {
                let amount = this.paymentAmount
                try {
                    let payment = await fetch(siteUrl+'/paycreate', {
                        method: 'POST',
                        body: JSON.stringify({
                            amount: amount
                        }),
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    })
                    let paymentData = await payment.json()
                    if (paymentData) {
                        let MyWindow = window.open(paymentData.url, 'MyWindow', 'width=600,height=300')
                        this.interval = setInterval(this.balance, 5000)
                    }
                } catch (error) {

                }
                return true
            }
        }
    })

});
