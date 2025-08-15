<template>

  <div v-if="isMounted">

    <gmap-map
      :center="mapNav.center"
      :options="{
           gestureHandling1: 'none',
           zoomControl: true,
           mapTypeControl: mapTypeControl,
           scaleControl: false,
           streetViewControl: false,
           rotateControl: false,
           fullscreenControl: true,
           disableDefaultUi: false,
           styles: mapStyles
         }"
      :style="{height: height}"
      :zoom="mapNav.zoom"
      :mapTypeId="mapType"
      @idle="onMapLoaded"
      ref="map"
      style="width: 100%;"
    >

      <gmap-info-window
        :opened="infoWindow.open"
        :options="{
            pixelOffset: {
              width: 0,
              height: -35
            }
          }"
        :position="infoWindow.pos"
        @closeclick="infoWindow.open=false"
        style="padding: 0;"
      >
        <component
          :entity="infoWindow.entity"
          @select="onSelect(infoWindow.entity)"
          is="CPopup"
          v-if="infoWindow.entity"
        ></component>
      </gmap-info-window>

      <template v-if="isMapLoaded">

        <template v-if="locked">

          <gmap-polygon
            :key="entity.nid"
            :label="'ffff'"
            :options="{
              geodesic: true,
              strokeColor: '#bfc7d5',
              fillColor: entity !== popupEntity ? '#f0f1f6' : '#3f75a2'
          }"
            :paths="regionPoligonPath(entity)"
            v-for="(entity) in compRegions"
            v-if="entity.coordinates"
          />

        </template>
        <template v-else>

          <gmap-polygon
            :key="entity.nid"
            :label="'ffff'"
            :options="{
              geodesic: true,
              strokeColor: '#bfc7d5',
              fillColor: entity !== popupEntity ? '#f0f1f6' : '#3f75a2'
          }"
            :paths="regionPoligonPath(entity)"
            @click="onSelect(entity)"
            @mouseover="onHoverRegion(entity)"
            v-for="(entity) in compRegions"
            v-if="entity.coordinates"
          />

        </template>


      </template>

    </gmap-map>

  </div>

</template>

<script>

  import CPopup from './components/popup';

  export default {
    props: {
      mapType: {default: 'satellite'},
      mapTypeControl: {default: true},
      height: {default: '500px'},
      zoom: {default: 5},
      center: {default: () => ({lat: 57.249499, lng: 104.289418})},
      openParams: {type: Object, default: () => ({})},
      entities: {},
      value: {},
      locked: {default: false}
    },
    components: {
      CPopup
    },
    data() {
      return {
        isMounted: false,
        isMapLoaded: false,

        valueInternal: this.value,
        popupInternal: this.value,

        infoWindow: {
          pos: null,
          open: false,
          entityType: null,
          entityId: null,
          entity: null,
        },

        mapNav: {
          zoom: this.zoom,
          center: this.center
        }
      }
    },
    computed: {

      activeEntity() {
        return this.valueInternal && this.compEntities.find(item => item.nid === this.valueInternal)
      },

      popupEntity() {
        return this.popupInternal && this.compEntities.find(item => item.nid === this.popupInternal)
      },

      mapStyles() {
        return [{
          stylers: [{
            color: '#ffffff'
          }, {
            visibility: 'simplified'
          }, {}, {
            weight1: 0.5
          }]
        }];
      },

      compRegions() {
        return this.compEntities.filter(entity => entity.mapType === 'region')
      },

      compPlaces() {
        return this.compEntities.filter(entity => entity.mapType === 'place')
      },

      compEntities() {
        return this.entities.filter((entity) => entity.field && entity.field.coordinates).map((entity) => {
          const coordinates = (typeof entity.field.coordinates === 'string') ? JSON.parse(entity.field.coordinates) : entity.field.coordinates;
          const mapType = this.getEntityMapType(entity, coordinates);
          return {
            ...entity,
            mapType,
            coordinates,
          }
        });
      }

    },
    watch: {

      valueInternal(val) {
        this.popupInternal = val
        this.$emit('input', val)
      },

      popupInternal(val) {
        this.$nextTick(() => {
          this.toggleInfoWindow(this.popupEntity)
        })
      },

      value(val) {
        this.valueInternal = val
        this.popupInternal = val
      },

      isMapLoaded(val) {
        if (val) {
          this.mapNavigateOverview();
        }
      },

    },
    methods: {

      onSelect(entity) {
        this.valueInternal = entity.nid
      },

      onHoverRegion(entity) {
        this.popupInternal = entity.nid
      },

      getEntityMapType(entity, coordinates) {
        if (Array.isArray(coordinates[0])) {
          return 'region'
        } else {
          return 'place'
        }
      },

      getPolygonCenter(data, format) {

        var bounds = new window.google.maps.LatLngBounds();

        var i;

        var polygonCoords = [];

        data.forEach((item) => {
          polygonCoords.push(new window.google.maps.LatLng(item[1], item[0]));
        });

        for (i = 0; i < polygonCoords.length; i++) {
          bounds.extend(polygonCoords[i]);
        }

        return bounds.getCenter();
      },

      mapNavigateOverview() {
        this.mapNav.zoom = this.zoom;
        this.mapNav.center = this.center;
      },

      regionPoligonPath(entity) {
        return entity.coordinates.map((item) => {
          return {
            lat: item[1],
            lng: item[0]
          }
        });
      },


      toggleInfoWindow(entity) {

        this.infoWindow.open = true
        this.infoWindow.entity = entity

        switch (entity.mapType) {

          case 'place':

            this.infoWindow.pos = {lat: entity.coordinates[1], lng: entity.coordinates[0]};

            break;

          case 'region':

            this.infoWindow.pos = this.getPolygonCenter(entity.coordinates);

            break;
        }

      },

      onMapLoaded() {
        this.isMapLoaded = true
      }

    },
    mounted() {
      this.isMounted = true;

      // this.activeRegion = this.entities[0]
    },
  }
</script>

<style lang="scss" scoped>

  .c-map-mode {
    position: absolute;
    top: 0;
    right: 100px;
    z-index: 100;
  }

  ::v-deep .gm-style .gm-style-iw-c {
    padding: 0;

    .gm-style-iw-d {
      overflow: visible !important;
    }

    button {
      display: none !important;
    }
  }

</style>
