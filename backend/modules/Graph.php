<?php

class Graph
{
	private $adj_mat;
	private $edges;
	private $nodes;

	private $nodesColor;
	private $maxColor;

	public function __construct()
	{
		$this->adj_mat = array();
		$this->edges = 0;
		$this->nodes = 0;

		$this->nodesColor = array();
		$this->maxColor = 0;
	}	

	public function nodeExists($name)
	{
		return isset($this->adj_mat[$name]);
	}

	public function addNode($name)
	{
		if (!$this->nodeExists($name))
		{
			$this->adj_mat[$name] = array();
			$this->nodes++;
			$this->nodesColor[$name] = 0;
		}
	}

	public function nodeCount()
	{
		return $this->nodes;
	}

	public function removeNode($name)
	{

		if(!$this->nodeExists($name))
		{
			return false;
		}

		foreach ($this->adj_mat[$name] as $node => $list)
		{
			$this->removeEdge($name, $node);
		} 

    	unset($this->adj_mat[$name]);
    	$this->nodes--;
	}

	public function degree($name)
	{
		if(!$this->nodeExists($name))
		{
			return false;
		}
		
		return count($this->adj_mat[$name]);
	}

	public function outDegree($name)
	{
		return $this->degree($name);
	}

	public function inDegree($name)
	{
	
		if(!$this->nodeExists($name))
		{
			return false;
		}

		$inDegree = 0;
		foreach ($this->adj_mat as $n => $list)
		{
			if ($n == $name) continue;
			if ($this->edgeExists($n, $name)) $inDegree++;
		}
	
		return $inDegree;
	}

	public function edgeExists($start, $end)
	{
		return $this->getEdge($start, $end) !== false;
	}

	public function edgeCount()
	{
		return $this->edges / 2; 
	}

	public function getEdge($start, $end)
	{
		if ($this->nodeExists($start) && $this->nodeExists($end))
		{
			if (isset($this->adj_mat[$start][$end])) return $this->adj_mat[$start][$end];
		}
		return false;
	}

	public function addEdge($start, $end, $weight = 1)
	{
		if (!$this->nodeExists($start))
		{
			$this->addNode($start);
		}

		if (!$this->nodeExists($end)) 
		{
			$this->addNode($end);
		}
		
		if ($this->nodeExists($start) && $this->nodeExists($end))
		{
			if (!isset($this->adj_mat[$start][$end])) $this->edges++;
			$this->adj_mat[$start][$end] = $weight;
		
			if(!isset($this->adj_mat[$end][$start])) $this->edges++;
			$this->adj_mat[$end][$start] = $weight;
		
			return true;
		}

		return false;
	}

	public function removeEdge($start, $end)
	{
		if ($this->nodeExists($start) && $this->nodeExists($end))
		{
			if (isset($this->adj_mat[$start][$end]))
			{
				unset($this->adj_mat[$start][$end]);
				$this->edges--;      
			}

			if (isset($this->adj_mat[$end][$start]))
			{
				unset($this->adj_mat[$end][$start]);
				$this->edges--;
			}

			return true;
		}

		return false;
	}

	private function colorNode($name, $color) 
	{
		if (!$this->nodeExists($name))
		{
			return false;
		}

		$this->nodesColor[$name] = $color;

		if (isset($this->adj_mat[$name])) 
		{
			// For each node associated with the current node
			foreach ($this->adj_mat[$name] as $destNode => $weight)
			{
				// Not colored yet
				if ($this->nodesColor[$destNode] == 0)
				{
					$this->colorNode($destNode, $color);
				}
			}
		}		
	}

	private function computeConnectedComponents()
	{
		$color = 1;

		// While an uncolored node exists
		while (($node = array_search(0, $this->nodesColor)) != false)
		{
			$this->colorNode($node, $color);
			$color++;
		}

		$this->maxColor = $color - 1;
	}

	public function getConnectedComponentsAsGraphs()
	{
		// Reinit colors
		array_fill_keys(array_keys($this->nodesColor), 0);

		// Compute connected components by coloring nodes
		$this->computeConnectedComponents();
		
		$connectedComponents = array();

		// Create the graphs
		for ($color = 1; $color <= $this->maxColor; $color++)
		{
			$graph = new Graph();

			$nodes = array_keys($this->nodesColor, $color);
			
			foreach ($nodes as $node)
			{
				$graph->addNode($node);
				
				foreach ($this->adj_mat[$node] as $destNode => $weight)
				{
					$graph->addEdge($node, $destNode, $weight);
				}
			}
			
			$connectedComponents[] = $graph;			
		}

		return $connectedComponents;
	}
	
	public function asArray()
	{
		$result = array();
		
		$allGraphNodes = array_keys($this->adj_mat);
		
		// Set the nodes
		$nodes = array();
		$idNode = 1;
		foreach ($allGraphNodes as $currentNode)
		{
			$nodes[] = array(
				'id' => $idNode,
				'label' => $currentNode
			);
		}
		
		$result['nodes'] = $nodes;
		
		// Set the edges
		$edges = array();
		$edgesAdded = array();
		
		foreach ($this->adj_mat as $startNode => $destNodes)
		{
			$idNodeFrom = array_search($startNode, $allGraphNodes);
			
			foreach ($destNodes as $destNode => $weight) 
			{
				$idNodeTo = array_search($destNode, $allGraphNodes);
				
				if (!in_array($idNodeTo . '-' . $idNodeFrom, $edgesAdded))
				{
					$edges[] = array(
						'from' => $idNodeFrom,
						'to' => array_search($destNode, $allGraphNodes),
						'label' => $weight
					);
					
					$edgesAdded[] = $idNodeFrom . '-' . $idNodeTo;
				}
			}
		}
		
		$result['edges'] = $edges;
		
		return $result;
	}
	
	
}

?>