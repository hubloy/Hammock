import React, { Component } from 'react';
import PropTypes from 'prop-types';

import Dashboard from './layout/Dashboard';
import {
	XYPlot,
	XAxis,
	YAxis,
	VerticalGridLines,
	HorizontalGridLines,
	VerticalBarSeries,
	VerticalBarSeriesCanvas,
	LabelSeries
} from 'react-vis';

export default class Admin extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		const greenData = [{ x: 'A', y: 10 }, { x: 'B', y: 5 }, { x: 'C', y: 15 }];

		const blueData = [{ x: 'A', y: 12 }, { x: 'B', y: 2 }, { x: 'C', y: 11 }];

		const labelData = greenData.map((d, idx) => ({
			x: d.x,
			y: Math.max(greenData[idx].y, blueData[idx].y)
		}));
		return (
			<Dashboard hammock={this.props.hammock}>
				<XYPlot xType="ordinal" width={300} height={300} xDistance={100}>
					<VerticalGridLines />
					<HorizontalGridLines />
					<XAxis />
					<YAxis />
					<VerticalBarSeries className="vertical-bar-series-example" data={greenData} />
					<VerticalBarSeries data={blueData} />
					<LabelSeries data={labelData} getLabel={d => d.x} />
				</XYPlot>
			</Dashboard>
		)
	}
}

Admin.propTypes = {
	hammock: PropTypes.object
};