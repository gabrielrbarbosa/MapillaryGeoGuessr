## Mapillary GeoGuessr
Free and open-source GeoGuessr alternative, using Mapillary JS instead of Google Maps' expensive APIs.
It consumes Mapillary API endpoint https://graph.mapillary.com/images then filters camera_type='spherical' and select 5 random places to play.

- TODO: Complete OAuth access and refresh tokens
- TODO: Game UI and show scores, distances like Geoguessr
- TODO: Multiplayer Free Parties (?)

## Setup
- Create new [Mapillary Account](https://www.mapillary.com/dashboard/developers)
- Create new Mapillary App and replace .env variables
- Run composer install

## License
[MIT](LICENSE)