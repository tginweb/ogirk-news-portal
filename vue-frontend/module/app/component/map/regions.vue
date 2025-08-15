<template>

  <div v-if="isMounted">

    <div class="c-body">

      <div class="row">

        <div class="col-5">

          <q-scroll-area
            :style="{height: '700px'}"
          >
            <div
              :key="entity.nid"
              v-for="entity of compEntities"
              class="q-mb-sm"
            >
              {{entity.name}}
            </div>
          </q-scroll-area>

        </div>

        <div class="col-19">

          <gmap-map
            :center="mapNav.center"
            :options="{
               gestureHandling: 'none',
               zoomControl: true,
               mapTypeControl: false,
               scaleControl: false,
               streetViewControl: false,
               rotateControl: false,
               fullscreenControl: true,
               disableDefaultUi: false,
               styles: mapStyles
             }"
            :zoom="mapNav.zoom"
            @idle="onMapLoaded"
            ref="map"
            style="width: 100%; height: 700px"
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
                is="CPopup"
                v-if="infoWindow.entity"
              ></component>
            </gmap-info-window>

            <template v-if="isMapLoaded">

              <gmap-polygon
                :key="entity.nid"
                :label="'ffff'"
                :options="{
                    geodesic: true,
                    strokeColor: '#bfc7d5',
                    fillColor: entity !== activeRegion ? '#f0f1f6' : '#3f75a2'
                }"
                :paths="regionPoligonPath(entity)"
                @mouseover="toggleInfoWindow(entity)"
                v-for="(entity) in compRegions"
                v-if="entity.coordinates"
              >
              </gmap-polygon>

            </template>

          </gmap-map>

        </div>

      </div>


    </div>

  </div>

</template>

<script>

  import CPopup from './components/popup';

  export default {
    props: {
      height: {default: '500px'},
      openParams: {type: Object, default: () => ({})},
      entities: {}
    },
    components: {
      CPopup
    },
    data() {
      return {
        activeNodes: [],
        isMounted: false,
        isMapLoaded: false,

        activeRegion: null,

        infoWindow: {
          pos: null,
          open: false,
          entityType: null,
          entityId: null,
          entity: null,
        },
        mapNav: {
          zoom: 5,
          center: {lat: 57.249499, lng: 104.289418}
        }
      }
    },
    computed: {

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
        return this.entities.filter((entity) => entity.coordinates).map((entity) => {
          const mapType = this.getEntityMapType(entity);
          return {
            ...entity,
            mapType,
            coordsCenter: mapType === 'region' && this.isMapLoaded ? this.getPolygonCenter(entity.coordinates) : null
          }
        });
      }

    },
    watch: {

      activeNodes(val) {
        this.activeRegion = this.compRegions.find((c) => c.ID == val[0]);
      },

      activeRegion(entity) {
        this.toggleInfoWindow(entity, entity.coordinatesCenter);
      },

      isMapLoaded(val) {
        if (val) {
          this.mapNavigateOverview();
        }
      },

    },
    methods: {

      getEntityMapType(entity) {
        if (entity.coordinates) {
          if (Array.isArray(entity.coordinates[0])) {
            return 'region'
          } else {
            return 'place'
          }
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
        this.mapNav.zoom = 5;
        this.mapNav.center = {lat: 57.249499, lng: 106.289418};
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

        this.activeRegion = entity;

        this.infoWindow.open = true;

        this.infoWindow.entity = entity;

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

  .c-head {

  }

  .c-body {
    position: relative;
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

  .c-panel-left {

    max-width: 390px;
  }

  .c-panel-right {

    max-width: 390px;
    background1: transparent;
  }

  .c-panel-closer {
    background: rgb(238, 234, 225) !important;
    padding: 0px 1px 0px 1px !important;
    position: absolute;
    left: 0;
    top: 0;
    height: 56px;
    z-index: 1000;
    border-radius: 0;
  }

  .c-map-hotels {
    iframe {
      height: 100%;
      margin-top: -15px !important;
    }
  }

  .c-regions {
    top: 40px;
    right: 20px;
    position: absolute;
    z-index: 100;

    .c-regions-title {
      font-weight: 500;
      margin: 0 0 10px 0;
    }

    .c-regions-item {

      a {
        color: #7f8993;
        padding: 3px 0px 3px 0px;
        text-decoration: none;

        &.active {
          color: #fff;
          background: #f66e59;
          padding: 3px 10px 3px 10px;
          margin-left: -10px;
          margin-right: -10px;
          display: block;
        }
      }
    }
  }

</style>
