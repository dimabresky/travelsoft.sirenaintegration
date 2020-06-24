
<style>
    .data-item__subitem {
        margin: 10px 0;
    }
    .pnr-order__title {
        font-size: 24px;
    }
    .subitem__passenger {
        padding: 10px;
    }
    .subitem__title {
        font-size: 20px;
        margin: 15px 0 5px 0;
    }
    .segment-departure__data {
        padding-left: 15px;
    }
</style>

<template id="pnr-order-template">

    <div class="pnr-order">
        <div class="alert alert-warning" v-if="loading"><b>Идет загрузка данных...</b></div>
        <div class="alert alert-danger" v-if="errors" v-html="errors"></div>
        <div class="pnr-order__title">Данные по бронировке билетов</div>
        <div v-if="pnrData" class="pnr-order__data pnr-order-data">

            <div v-for="data in pnrData" class="pnr-order-data__item data-item">

                <div class="data-item__subitem">Идентификатор PNR: <b>{{data.regnum}}</b></div>
                <div class="data-item__subitem">Код агентства, создавшего PNR: <b>{{data.agency}}</b></div>
                <div class="data-item__subitem">Стоимость: <b>{{data.price}}</b></div>
                <div class="data-item__subitem subitem">
                    <div class="subitem__title">Данные пассажиров</div>
                    <span class="subitem__passenger" v-for="passenger in data.passengers">{{[`${passenger.name} ${passenger.surname}`, passenger.sex == 'male' ? 'Мужской' : 'Женский', passenger.birthdate, `${passenger.age} лет`].join(', ')}}<br></span>
                </div> 
                <div class="data-item__subitem subitem">
                    <div class="subitem__title">Данные по сегментам перелета</div>
                    <div class="row">
                        <div v-for="segment in data.segments" class="segment col-md-6">

                            <div class="segment__company">Код маркетингового перевозчика: <b>{{segment.company[0]}}</b></div>
                            <div class="segment__flight">Номер рейса маркетингового перевозчика: <b>{{segment.flight[0]}}</b></div>
                            <div class="segment__class">Класс сегмента: <b>{{segment.class == 'E' ? 'Эконом' : segment.class}}</b></div>
                            <div class="segment__seatcount">Кол-во мест, забронированных на сегменте: <b>{{segment.seatcount[0]}}</b></div>
                            <div class="segment__departure segment-departure">
                                Данные отпраления: 
                                <div class="segment-departure__data">Аэропорт: <b>{{segment.departure.airport || '-'}}</b></div>
                                <div class="segment-departure__data">Город: <b>{{segment.departure.city}}</b></div>
                                <div class="segment-departure__data">Дата: <b>{{segment.departure.date}}</b></div>
                                <div class="segment-departure__data">Терминал: <b>{{segment.departure.terminal}}</b></div>
                                <div class="segment-departure__data">Время: <b>{{segment.departure.time}}</b></div>
                            </div>
                            <div class="segment__arrival">
                                Данные прибытия: 
                                <div class="segment-departure__data">Аэропорт: <b>{{segment.departure.airport || '-'}}</b></div>
                                <div class="segment-departure__data">Город: <b>{{segment.arrival.city}}</b></div>
                                <div class="segment-departure__data">Дата: <b>{{segment.arrival.date}}</b></div>
                                <div class="segment-departure__data">Терминал: <b>{{segment.arrival.terminal}}</b></div>
                                <div class="segment-departure__data">Время: <b>{{segment.arrival.time}}</b></div>
                            </div>
                            <div class="segment__status">Статус: <b>{{segment.status[0]}}</b></div>
                            <div class="segment__flightTime">Время в пути: <b>{{segment.flightTime[0]}}</b></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    (() => {

        let request = (url, data) => {
            return new Promise((resolve, reject) => {
                BX.ajax.post(url, data, (resp) => {
                    resp = JSON.parse(resp);
                    if (resp.error) {
                        return reject(resp.message);
                    } else {
                        return resolve(resp.result);
                    }
                });
            });
        };

        BX.Vue.component('pnr-order', {
            template: document.getElementById('pnr-order-template').innerHTML,
            data() {
                return {
                    loading: true,
                    pnrData: null,
                    errors: null
                };
            },
            mounted() {
                this.loadPnrData();
            },
            methods: {
                loadPnrData() {

                    request('/local/modules/travelsoft.sirenaintegration/views/pnr-order/ajax.php', {
                        servicesBookId: <?= json_encode($parameters['servicesBookId']) ?>,
                    }).then(result => {
                        this.loading = false;

                        this.pnrData = result;
                    }).catch(message => {
                        this.errors = message;
                        this.loading = false;
                    });
                }
            }
        });
    })();
</script>
