<template>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h1>Relations</h1>
        </div>
        <div class="panel-body">
            <div v-for="relation in relationTypes">
                <dl class="dl-horizontal">
                    <dt>Nom</dt>
                    <dd>{{ relation.name }}</dd>
                    <dt>Nom étendu</dt>
                    <dd>{{ relation.nom_etendu }}</dd>
                    <dt>Info</dt>
                    <dd>{{ relation.info }}</dd>
                    <dt>Identifiant</dt>
                    <dd><span class="badge badge-info">{{ relation._id }}</span></dd>
                </dl>
            </div>
        </div>
    </div>
</template>
<script>
    import { HUB } from '../vue/data.js';

    export default {
        props: ['relationTypes'],
        beforeCreate()
        {
            var title = HUB.$data.shared.htmlTitle;
            $('title').text(title + ' - Relations');
        },
        destroyed()
        {
            $('title').text(HUB.$data.shared.htmlTitle);
        }
    };
</script>