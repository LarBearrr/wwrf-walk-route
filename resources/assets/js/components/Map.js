import React, { Component } from 'react';
import ReactDOM from 'react-dom';

export default class Map extends Component {
    render() {
        const GoogleMapExample = withGoogleMap(props => (
            <GoogleMap
              defaultCenter = { { lat: 40.756795, lng: -73.954298 } }
              defaultZoom = { 13 }
            >
            </GoogleMap>
         ));
        return (
            <div className="container">
                <div className="row">
                    <div className="col-md-8 col-md-offset-2">
                        <div className="panel panel-default">
                            <GoogleMapExample
                                containerElement={ <div style={{ height: `500px`, width: '500px' }} /> }
                                mapElement={ <div style={{ height: `100%` }} /> }
                            />
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

if (document.getElementById('map')) {
    ReactDOM.render(<Map />, document.getElementById('map'));
}