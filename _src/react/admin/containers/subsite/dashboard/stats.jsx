import React, { Component } from 'react';

import fetchWP from 'utils/fetchWP';

import {Preloader, Center} from 'ui/admin/form';
import { Bar, Line } from 'react-chartjs-2';

export default class StatsDashboard extends Component {

	constructor(props) {
		super(props);
		this.state = {
			loading_subs : true,
			loading_trans : true,
			subscribers : [],
			transactions : []
        };
        this.fetchWP = new fetchWP({
			api_url: this.props.hammock.api_url,
			api_nonce: this.props.hammock.api_nonce,
        });
	}


	componentDidMount() {
		Promise.all([this.load_subscribers(), this.load_transactions()]);
	}

	load_subscribers = async() => {
		this.fetchWP.get( 'dashboard/subscribers' )
			.then( (json) => this.setState({
				subscribers : json,
				loading_subs : false,
				error : false,
			}), (err) => this.setState({ loading_subs : false, error : true })
		);
	}

	load_transactions = async() => {
		this.fetchWP.get( 'dashboard/transactions' )
			.then( (json) => this.setState({
				transactions : json,
				loading_trans : false,
				error : false,
			}), (err) => this.setState({ loading_trans : false, error : true })
		);
	}


	render() {
		var subscribers = this.state.subscribers;
		var transactions = this.state.transactions;
		var hammock = this.props.hammock,
			days = hammock.strings.stats.charts.days;
		const data = {
			labels: [days.mon, days.tue, days.wed, days.thu, days.fri, days.sat, days.sun],
			datasets: [
				{
					label: hammock.strings.stats.charts.subscribers,
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
			labels: [days.mon, days.tue, days.wed, days.thu, days.fri, days.sat, days.sun],
			datasets: [
				{
					label: hammock.strings.stats.charts.transactions,
					data: [12, 19, 3, 5, 2, 3, 20],
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
			<div className="uk-background-default uk-padding-small uk-panel" uk-height-viewport="min-height: 800">
				<p className="uk-h4">{hammock.strings.stats.title}</p>
				<div>
					<Bar data={data} options={options}/>
				</div>
				<hr/>
				<div>
					<Line data={linedata} options={lineoptions} />
				</div>
			</div>
		)
	}
}