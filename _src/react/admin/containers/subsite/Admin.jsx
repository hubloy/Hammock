import React, { Component } from 'react';
import PropTypes from 'prop-types';

import Dashboard from './layout/Dashboard';
import { Bar, Line } from 'react-chartjs-2';


export default class Admin extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		const data = {
			labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
			datasets: [
				{
					label: '# of Subscribers',
					data: [12, 19, 3, 5, 2, 3, 20],
					backgroundColor: 'rgb(49, 104, 142)',
					borderColor: 'rgb(49, 104, 142)',
					borderWidth: 1,
				},
			],
		};

		const options = {
			scales: {
				yAxes: [
					{
						ticks: {
							beginAtZero: true,
						},
					},
				],
			},
			plugins: {
				legend: {
					onClick: function(e, item) {
						return;
					}
				}
			}
		};

		const linedata = {
			labels: ['1', '2', '3', '4', '5', '6'],
			datasets: [
				{
					label: '# of Transactions',
					data: [12, 19, 3, 5, 2, 3],
					fill: false,
					backgroundColor: 'rgb(49, 104, 142)',
					borderColor: 'rgba(49, 104, 142)',
				},
			],
		};

		const lineoptions = {
			scales: {
				yAxes: [
					{
						ticks: {
							beginAtZero: true,
						},
					},
				],
			},
			plugins: {
				legend: {
					onClick: function(e, item) {
						return;
					}
				}
			}
		};

		return (
			<Dashboard hammock={this.props.hammock}>
				<div uk-grid="">
					<div className="uk-width-1-2@m uk-width-1-1@s">
						<div className="uk-background-default uk-padding-small uk-panel uk-height-medium">
							<p className="uk-h4">Members <span className="hammock-badge-circle">100</span></p>
							<div>
								<ul className="uk-list uk-list-striped">
									<li>Member 1</li>
									<li>Member 2</li>
									<li>Member 3</li>
									<li>Member 4</li>
								</ul>
							</div>
						</div>
						<div className="uk-background-default uk-margin-medium-top uk-padding-small uk-panel uk-height-medium">
							<p className="uk-h4">Memberships <span className="hammock-badge-circle">100</span></p>
							<div>
								<ul className="uk-list uk-list-striped">
									<li>Membership 1</li>
									<li>Membership 2</li>
									<li>Membership 3</li>
									<li>Membership 4</li>
								</ul>
							</div>
						</div>
						<div className="uk-background-default uk-margin-medium-top uk-padding-small uk-panel uk-height-medium">
							<p className="uk-h4">Site Management</p>
							<div>
								<a><div className="uk-margin-small uk-padding-small uk-card uk-card-default uk-card-body">Add Memberships</div></a>
								<a><div className="uk-margin-small uk-padding-small uk-card uk-card-default uk-card-body">View Addons</div></a>
							</div>
						</div>
					</div>
					<div className="uk-width-1-2@m uk-width-1-1@s">
						<div className="uk-background-default uk-padding-small uk-panel" uk-height-viewport="min-height: 800">
							<p className="uk-h4">Stats Overview</p>
							<div>
								<Bar data={data} options={options}/>
							</div>
							<hr/>
							<div>
								<Line data={linedata} options={lineoptions} />
							</div>
						</div>
					</div>
				</div>
			</Dashboard>
		)
	}
}

Admin.propTypes = {
	hammock: PropTypes.object
};